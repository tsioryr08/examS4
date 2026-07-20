<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\OperateurModel;

class ClientController extends BaseController
{
    public function login()
    {
        return view('client/login');
    }

    public function verifier()
    {
        $numero = trim($this->request->getPost('numero'));

        //numero a 10 chiffres 
        if (empty($numero) || !preg_match('/^0\d{9}$/', $numero)) {
            return redirect()->to('/login')->with('error', 'Numéro invalide. Format attendu : 10 chiffres');
        }

        //prendre le prefixe pour verifier il est dans quel operateur 
        $prefixe = substr($numero, 0, 3);

        $operateurModel = new OperateurModel();
        $operateur = $operateurModel->where('code', $prefixe)->first();

        if (!$operateur) {
            return redirect()->to('/login')->with('error', 'Ce préfixe n\'est reconnu par aucun opérateur.');
        }

        //Chercher le premier client qui correspond au numero saisi 
        $clientModel = new ClientModel();
        $client = $clientModel->where('numero', $numero)->first();

        //si le numero saisi n existe pas, on create avec solde 0 avec l'id qui correspond
        if (!$client) {
            $clientId = $clientModel->insert([
                'numero'       => $numero,
                'solde'        => 0,
                'id_operateur' => $operateur['id'],
            ]);

            $client = $clientModel->find($clientId);
        }

        //sessioner pour recup les infos du client co
        session()->set('client_id', $client['id']);
        session()->set('client_numero', $client['numero']);
        session()->set('operateur_id', $client['id_operateur']);

        return redirect()->to('/dashboard');
    }

//pouravoir les notifs de transfert recu
        public function dashboard()
    {
        $clientId = session()->get('client_id');

        if (!$clientId) {
            return redirect()->to('/login');
        }

        $clientModel = new ClientModel();
        $client = $clientModel->find($clientId);

        if (!$client) {
            session()->destroy();
            return redirect()->to('/login');
        }

        $operationModel = new \App\Models\OperationModel();
        $idTypeTransfert = $this->getIdTypeOperation('transfert');
//pour avoir les petites notifs de transfert recu
        // Tous les transferts recus par ce client
        $transfertsRecus = $operationModel
            ->where('destinataire_id', $clientId)
            ->where('id_type_operation', $idTypeTransfert)
            ->orderBy('date_operation', 'DESC')
            ->findAll();

        // Liste des id deja notifies, stockee en session
        $dejaNotifies = session()->get('notifs_transferts_vus') ?? [];

        $notifications = [];
        $nouveauxIds = $dejaNotifies;

        foreach ($transfertsRecus as $op) {
            if (!in_array($op['id'], $dejaNotifies)) {
                $expediteur = $clientModel->find($op['id_client']);
                $notifications[] = [
                    'montant' => $op['montant'],
                    'numero'  => $expediteur['numero'] ?? 'numéro inconnu',
                ];

                $nouveauxIds[] = $op['id'];
            }
        }

        session()->set('notifs_transferts_vus', $nouveauxIds);

        $historique = $operationModel
            ->where('id_client', $clientId)
            ->orderBy('date_operation', 'DESC')
            ->findAll(10);

        return view('client/dashboard', [
            'client'        => $client,
            'historique'    => $historique,
            'notifications' => $notifications,
        ]);
    }

    // pour les operations 
    //1.depot

    public function depot()
    {
        if (!session()->get('client_id')) {
            return redirect()->to('/login');
        }

        return view('client/depot');
    }

    public function depotValider()
    {
        $clientId = session()->get('client_id');

        if (!$clientId) {
            return redirect()->to('/login');
        }

        $montant = (float) $this->request->getPost('montant');

        if ($montant <= 0) {
            return redirect()->to('/depot')->with('error', 'Montant doit etre positif.');
        }

        $clientModel = new ClientModel();
        $client = $clientModel->find($clientId);

        if (!$client) {
            session()->destroy();
            return redirect()->to('/login');
        }

        //maj du solde(via une transaction pour garder la coherence des montants)
        $this->db = \Config\Database::connect();
        $idTypeDepot = $this->getIdTypeOperation('depot');
        $this->db->transStart();

        $nouveauSolde = $client['solde'] + $montant;

        $clientModel->update($clientId, [
            'solde' => $nouveauSolde,
        ]);

        // 2. Enregistrer l'operation dans l'historique
        $operationModel = new \App\Models\OperationModel();
        $operationModel->insert([
            'id_client'          => $clientId,
            'destinataire_id'    => null,
            'id_type_operation'  => $idTypeDepot,
            'montant'            => $montant,
            'frais'              => 0, // tt les depots gratuits
            'date_operation'     => date('Y-m-d H:i:s'),
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->to('/depot')->with('error', 'Une erreur est survenue, réessaie.');
        }

        return redirect()->to('/dashboard')->with('success', 'Dépôt de ' . number_format($montant, 0, ',', ' ') . ' Ar effectué.');
    }

    public function retrait()
{
    $clientId = session()->get('client_id');
    if (!$clientId) {
        return redirect()->to('/login');
    }

    $clientModel = new ClientModel();
    $client = $clientModel->find($clientId);

    return view('client/retrait', ['client' => $client]);
}

public function retraitValider()
{
    $clientId = session()->get('client_id');
    if (!$clientId) {
        return redirect()->to('/login');
    }

    $montant = (float) $this->request->getPost('montant');

    if ($montant <= 0) {
        return redirect()->to('/retrait')->with('error', 'Montant invalide.');
    }

    $clientModel = new ClientModel();
    $client = $clientModel->find($clientId);

    if (!$client) {
        session()->destroy();
        return redirect()->to('/login');
    }

    // Recuperer dynamiquement l'id du type d'operation "retrait"
    $idTypeRetrait = $this->getIdTypeOperation('retrait');

    // Calculer le frais selon le bareme de l'operateur du client
    $frais = $this->calculerFrais($montant, $client['id_operateur'], $idTypeRetrait);
    $totalADeduire = $montant + $frais;

    // Verification du solde AVANT de toucher a quoi que ce soit
    if ($client['solde'] < $totalADeduire) {
        return redirect()->to('/retrait')->with('error', 'Solde insuffisant pour ce retrait (montant + frais).');
    }

    $this->db = \Config\Database::connect();
    $this->db->transStart();

    $nouveauSolde = $client['solde'] - $totalADeduire;

    $clientModel->update($clientId, [
        'solde' => $nouveauSolde,
    ]);

    $operationModel = new \App\Models\OperationModel();
    $operationModel->insert([
        'id_client'         => $clientId,
        'destinataire_id'   => null,
        'id_type_operation' => $idTypeRetrait,
        'montant'           => $montant,
        'frais'             => $frais,
        'date_operation'    => date('Y-m-d H:i:s'),
    ]);

    $this->db->transComplete();

    if ($this->db->transStatus() === false) {
        return redirect()->to('/retrait')->with('error', 'Une erreur est survenue, réessaie.');
    }

    return redirect()->to('/dashboard')->with('success',
        'Retrait de ' . number_format($montant, 0, ',', ' ') . ' Ar effectué (frais : ' . number_format($frais, 0, ',', ' ') . ' Ar).');
}

private function calculerFrais(float $montant, int $idOperateur, int $idTypeOperation): float
{
    $baremeModel = new \App\Models\BaremeFraisModel();

    $bareme = $baremeModel
        ->where('id_operateur', $idOperateur)
        ->where('id_type_operation', $idTypeOperation)
        ->where('montant_min <=', $montant)
        ->where('montant_max >=', $montant)
        ->first();

    if (!$bareme) {
        return 0;
    }

    return (float) $bareme['frais'];
}

private function getIdTypeOperation(string $nom): int
{
    $typeModel = new \App\Models\TypeOperationModel();
    $type = $typeModel->where('nom', $nom)->first();

    if (!$type) {
        throw new \RuntimeException("Type d'operation '{$nom}' introuvable en base.");
    }

    return (int) $type['id'];
}


        public function transfert()
{
    $clientId = session()->get('client_id');
    if (!$clientId) {
        return redirect()->to('/login');
    }

    $clientModel = new ClientModel();
    $client = $clientModel->find($clientId);

    return view('client/transfert', ['client' => $client]);
}

public function transfertValider()
{
    $clientId = session()->get('client_id');
    if (!$clientId) {
        return redirect()->to('/login');
    }

    $numeroDestinataire = trim($this->request->getPost('numero_destinataire'));
    $montant = (float) $this->request->getPost('montant');

    if ($montant <= 0) {
        return redirect()->to('/transfert')->with('error', 'Montant invalide.');
    }

    $clientModel = new ClientModel();
    $client = $clientModel->find($clientId);

    if (!$client) {
        session()->destroy();
        return redirect()->to('/login');
    }

    // Empecher de se transferer a soi-meme
    if ($numeroDestinataire === $client['numero']) {
        return redirect()->to('/transfert')->with('error', 'Impossible de transférer vers votre propre numéro.');
    }

    // Verifier que le destinataire existe
    $destinataire = $clientModel->where('numero', $numeroDestinataire)->first();

    if (!$destinataire) {
        return redirect()->to('/transfert')->with('error', 'Ce numéro de destinataire n\'existe pas.');
    }

    // Calculer le frais selon le bareme de l'operateur de l'EXPEDITEUR
    $idTypeTransfert = $this->getIdTypeOperation('transfert');
    $frais = $this->calculerFrais($montant, $client['id_operateur'], $idTypeTransfert);
    $totalADeduire = $montant + $frais;

    // Verification du solde AVANT de toucher a quoi que ce soit
    if ($client['solde'] < $totalADeduire) {
        return redirect()->to('/transfert')->with('error', 'Solde insuffisant pour ce transfert (montant + frais).');
    }

    $this->db = \Config\Database::connect();
    $this->db->transStart();

    // 1. Debiter l'expediteur (montant + frais)
    $clientModel->update($clientId, [
        'solde' => $client['solde'] - $totalADeduire,
    ]);

    // 2. Crediter le destinataire (montant net, sans frais)
    $clientModel->update($destinataire['id'], [
        'solde' => $destinataire['solde'] + $montant,
    ]);

    // 3. Enregistrer l'operation dans l'historique
    $operationModel = new \App\Models\OperationModel();
    $operationModel->insert([
        'id_client'         => $clientId,
        'destinataire_id'   => $destinataire['id'],
        'id_type_operation' => $idTypeTransfert,
        'montant'           => $montant,
        'frais'             => $frais,
        'date_operation'    => date('Y-m-d H:i:s'),
    ]);

    $this->db->transComplete();

    if ($this->db->transStatus() === false) {
        return redirect()->to('/transfert')->with('error', 'Une erreur est survenue, réessaie.');
    }

    return redirect()->to('/dashboard')->with('success',
        'Transfert de ' . number_format($montant, 0, ',', ' ') . ' Ar vers ' . esc($numeroDestinataire) . ' effectué (frais : ' . number_format($frais, 0, ',', ' ') . ' Ar).');
}

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

}