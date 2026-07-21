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

    private function getNomAffichage(array $client): string
    {
    $nomComplet = trim(($client['nom'] ?? '') . ' ' . ($client['prenom'] ?? ''));
    return $nomComplet !== '' ? $nomComplet : $client['numero'];
    }

        public function verifier()
{
    $numero = trim($this->request->getPost('numero'));

    //format num malagasy
    if (empty($numero) || !preg_match('/^0\d{9}$/', $numero)) {
        return redirect()->to('/login')->with('error', 'Numéro invalide. Format attendu : 0331234567');
    }

    $prefixe = substr($numero, 0, 3);

    //Verifier que le prefixe correspond a NOTRE operateur
    $operateurModel = new OperateurModel();
    $operateur = $operateurModel->where('code', $prefixe)->first();

    if (!$operateur) {
        return redirect()->to('/login')->with('error', 'Ce préfixe n\'est pas pris en charge par notre opérateur.');
    }

    //Chercher le client existant
    $clientModel = new ClientModel();
    $client = $clientModel->where('numero', $numero)->first();

    //creer si inconnu (solde 0)
    if (!$client) {
        $clientId = $clientModel->insert([
            'numero'       => $numero,
            'solde'        => 0,
            'id_operateur' => $operateur['id'],
        ]);

        $client = $clientModel->find($clientId);
    }

    // 5. Ouvrir la session
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
            'nomAffiche'    =>$this->getNomAffichage($client),
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

        //Enregistrer l'operation dans l'historique
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
        return redirect()->to('/retrait')->with('error', 'Montant doit etre positif.');
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
    $inclureFraisRetrait = (bool) $this->request->getPost('inclure_frais_retrait');

    if ($montant <= 0) {
        return redirect()->to('/transfert')->with('error', 'Montant invalide.');
    }

    $clientModel = new ClientModel();
    $client = $clientModel->find($clientId);

    if (!$client) {
        session()->destroy();
        return redirect()->to('/login');
    }
//envoi du vola vers toi meme
    if ($numeroDestinataire === $client['numero']) {
        return redirect()->to('/transfert')->with('error', 'Impossible de transférer vers votre propre numéro.');
    }

    $prefixeDestinataire = substr($numeroDestinataire, 0, 3);
    $idTypeTransfert = $this->getIdTypeOperation('transfert');

    // Determiner si le destinataire est chez NOTRE operateur
    $operateurModel = new OperateurModel();
    $operateurDestinataire = $operateurModel->where('code', $prefixeDestinataire)->first();

    $fraisTransfert = $this->calculerFrais($montant, $client['id_operateur'], $idTypeTransfert);

    if ($operateurDestinataire) {
        //si meme operateur que l'envoyeur
        $clientDestModel = new ClientModel();
        $destinataire = $clientDestModel->where('numero', $numeroDestinataire)->first();

        if (!$destinataire) {
            return redirect()->to('/transfert')->with('error', 'Ce numéro de destinataire n\'existe pas.');
        }

        //enlever juste le if sion veut que ca soit autom sans checkbox
        $fraisRetrait = 0;
        if ($inclureFraisRetrait) {
            $idTypeRetrait = $this->getIdTypeOperation('retrait');
            $fraisRetrait = $this->calculerFrais($montant, $destinataire['id_operateur'], $idTypeRetrait);
        }

        $totalADeduire = $montant + $fraisTransfert + $fraisRetrait;

        if ($client['solde'] < $totalADeduire) {
            return redirect()->to('/transfert')->with('error', 'Solde insuffisant.');
        }

        $this->db = \Config\Database::connect();
        $this->db->transStart();

        // Debiter expediteur (montant + frais transfert + frais retrait anticipes)
        $clientModel->update($clientId, ['solde' => $client['solde'] - $totalADeduire]);

        // Crediter destinataire (montant + frais retrait anticipes si option cochee)
        $repartition = $this->repart_epargne($montant, $destinataire);

        $clientDestModel->update($destinataire['id'], [
            'solde' => $destinataire['solde'] + $repartition['solde'] + $fraisRetrait,
            'epargne' => $destinataire['epargne'] + $repartition['epargne'],
        ]);

        $operationModel = new \App\Models\OperationModel();
        $operationModel->insert([
            'id_client'          => $clientId,
            'destinataire_id'    => $destinataire['id'],
            'id_type_operation'  => $idTypeTransfert,
            'montant'            => $montant,
            'frais'              => $fraisTransfert,
            'frais_retrait'      => $fraisRetrait,
            'id_autre_operateur' => null,
            'date_operation'     => date('Y-m-d H:i:s'),
        ]);

        $this->db->transComplete();

        $messageMontant = $montant;

    } else {
        //si autre_operateur
        $autreOperateurModel = new \App\Models\AutreOperateurModel();
        $autreOperateur = $autreOperateurModel->where('code', $prefixeDestinataire)->first();

        if (!$autreOperateur) {
            return redirect()->to('/transfert')->with('error', 'Numéro de destinataire non reconnu.');
        }

        // Bloquer si le checkbox "frais de retrait" est coche : impossible pour un autre operateur
        if ($inclureFraisRetrait) {
            return redirect()->to('/transfert')->with('error',
                'Impossible d\'inclure les frais de retrait pour un transfert vers un autre opérateur (' . esc($autreOperateur['libelle']) . ').');
        }

        // Recuperer le taux de commission pour cet autre operateur + ce type d'operation
        $commissionModel = new \App\Models\CommissionModel();
        $commission = $commissionModel
            ->where('id_autre_operateur', $autreOperateur['id'])
            ->where('id_type_operation', $idTypeTransfert)
            ->first();

        if (!$commission) {
            return redirect()->to('/transfert')->with('error',
                'Aucune commission configurée pour ' . esc($autreOperateur['libelle']) . '.');
        }

        $montantCommission = $montant * ($commission['pourcent_commit'] / 100);

        // Pas de frais de retrait pour un autre operateur (regle v2)
        $totalADeduire = $montant + $fraisTransfert + $montantCommission;

        if ($client['solde'] < $totalADeduire) {
            return redirect()->to('/transfert')->with('error', 'Solde insuffisant.');
        }

        $this->db = \Config\Database::connect();
        $this->db->transStart();

        // Debiter l'expediteur uniquement (pas de client destinataire chez nous)
        $clientModel->update($clientId, ['solde' => $client['solde'] - $totalADeduire]);

        $operationModel = new \App\Models\OperationModel();
        $operationModel->insert([
            'id_client'          => $clientId,
            'destinataire_id'    => null,
            'id_type_operation'  => $idTypeTransfert,
            'montant'            => $montant,
            'frais'              => $fraisTransfert + $montantCommission,
            'frais_retrait'      => null,
            'id_autre_operateur' => $autreOperateur['id'],
            'date_operation'     => date('Y-m-d H:i:s'),
        ]);

        $this->db->transComplete();

        $messageMontant = $montant;
    }

    if ($this->db->transStatus() === false) {
        return redirect()->to('/transfert')->with('error', 'Une erreur est survenue, réessaie.');
    }

    return redirect()->to('/dashboard')->with('success',
        'Transfert de ' . number_format($messageMontant, 0, ',', ' ') . ' Ar effectué.');
}


        public function transfertMultiple()
{
    $clientId = session()->get('client_id');
    if (!$clientId) {
        return redirect()->to('/login');
    }

    $clientModel = new ClientModel();
    $client = $clientModel->find($clientId);

    return view('client/transfert_multiple', ['client' => $client]);
}




public function transfertMultipleValider()
{
    $clientId = session()->get('client_id');
    if (!$clientId) {
        return redirect()->to('/login');
    }

    $montantTotal = (float) $this->request->getPost('montant_total');
    $numerosRaw = $this->request->getPost('numeros');
    $inclureFraisRetrait = (bool) $this->request->getPost('inclure_frais_retrait');

    if ($montantTotal <= 0) {
        return redirect()->to('/transfert/multiple')->with('error', 'Montant invalide.');
    }

    $numeros = array_filter(array_map('trim', explode("\n", $numerosRaw)));
    $numeros = array_values(array_unique($numeros));

    $nbDestinataires = count($numeros);

    if ($nbDestinataires === 0) {
        return redirect()->to('/transfert/multiple')->with('error', 'Aucun numéro fourni.');
    }

    $clientModel = new ClientModel();
    $client = $clientModel->find($clientId);

    if (!$client) {
        session()->destroy();
        return redirect()->to('/login');
    }

    //Partage EQUITABLE avec arrondi vers le bas
    $montantParPersonne = floor($montantTotal / $nbDestinataires);

    // Ce qui sera reellement distribue (peut etre legerement inferieur au montant tape)car on a arrondi
    $montantReellementDistribue = $montantParPersonne * $nbDestinataires;

    // Le reste ne sera jamais debite, il reste chez l'expediteur
    $reste = $montantTotal - $montantReellementDistribue;

    $operateurModel = new OperateurModel();
    $idTypeTransfert = $this->getIdTypeOperation('transfert');
    $idTypeRetrait = $this->getIdTypeOperation('retrait');

    $destinataires = [];
    $fraisTransfertTotal = 0;
    $fraisRetraitTotal = 0;

    foreach ($numeros as $numero) {

        if ($numero === $client['numero']) {
            return redirect()->to('/transfert/multiple')->with('error',
                'Vous ne pouvez pas vous inclure vous-même dans la liste.');
        }

        // Meme operateur uniquement (regle v2)
        $prefixe = substr($numero, 0, 3);
        $operateurDest = $operateurModel->where('code', $prefixe)->first();

        if (!$operateurDest || $operateurDest['id'] !== $client['id_operateur']) {
            return redirect()->to('/transfert/multiple')->with('error',
                'Le numéro ' . esc($numero) . ' n\'appartient pas au même opérateur. Envoi multiple limité au même opérateur.');
        }

        $destinataire = $clientModel->where('numero', $numero)->first();

        if (!$destinataire) {
            return redirect()->to('/transfert/multiple')->with('error',
                'Le numéro ' . esc($numero) . ' n\'existe pas.');
        }

        // Frais de transfert sur la part individuelle (chaque part suit sa propre tranche)
        $fraisTransfert = $this->calculerFrais($montantParPersonne, $client['id_operateur'], $idTypeTransfert);

        // Frais de retrait seulement si le checkbox global est coche
        $fraisRetrait = 0;
        if ($inclureFraisRetrait) {
            $fraisRetrait = $this->calculerFrais($montantParPersonne, $destinataire['id_operateur'], $idTypeRetrait);
        }

        $destinataires[] = [
            'client'         => $destinataire,
            'frais_transfert'=> $fraisTransfert,
            'frais_retrait'  => $fraisRetrait,
        ];

        $fraisTransfertTotal += $fraisTransfert;
        $fraisRetraitTotal += $fraisRetrait;
    }

    // L'expediteur paie : le montant REELLEMENT distribue (pas le montant tape) + tous les frais
    $totalADeduire = $montantReellementDistribue + $fraisTransfertTotal + $fraisRetraitTotal;

    if ($client['solde'] < $totalADeduire) {
        return redirect()->to('/transfert/multiple')->with('error',
            'Solde insuffisant pour cet envoi (total requis : ' .
            number_format($totalADeduire, 0, ',', ' ') . ' Ar).');
    }

    $this->db = \Config\Database::connect();
    $this->db->transStart();

    // Debiter l'expediteur une seule fois pour le total reellement distribue
    $clientModel->update($clientId, [
        'solde' => $client['solde'] - $totalADeduire,
    ]);

    $operationModel = new \App\Models\OperationModel();

    foreach ($destinataires as $item) {
        $dest = $item['client'];
        $fraisTransfert = $item['frais_transfert'];
        $fraisRetrait = $item['frais_retrait'];

        // Chaque destinataire recoit sa part egale + frais de retrait anticipes si option cochee
            $repartition = $this->repart_epargne($montantParPersonne, $dest);

            $clientModel->update($dest['id'],[
                'solde' => $dest['solde']+ $repartition['solde'] + $fraisRetrait,
                'epargne'=> $dest['epargne'] + $repartition['epargne'],
            ]);

        $operationModel->insert([
            'id_client'         => $clientId,
            'destinataire_id'   => $dest['id'],
            'id_type_operation' => $idTypeTransfert,
            'montant'           => $montantParPersonne,
            'frais'             => $fraisTransfert + $fraisRetrait,
            'date_operation'    => date('Y-m-d H:i:s'),
        ]);
    }

    $this->db->transComplete();

    if ($this->db->transStatus() === false) {
        return redirect()->to('/transfert/multiple')->with('error', 'Une erreur est survenue, réessaie.');
    }

    $messageReste = $reste > 0
        ? ' (reste non distribué de ' . number_format($reste, 0, ',', ' ') . ' Ar, conservé sur votre compte)'
        : '';

    return redirect()->to('/dashboard')->with('success',
        'Envoi de ' . number_format($montantReellementDistribue, 0, ',', ' ') . ' Ar réparti équitablement entre ' . $nbDestinataires . ' destinataires effectué.' . $messageReste);
}

//epargne
    private function repart_epargne(float $montant, array $client):array{
        $pourcent_epargne = (int) ($client['pourcent_epargne']??0);
        $montantEpargne = $montant * ($pourcent_epargne /100);
        $montantSolde = $montant - $montantEpargne;

        return[
            'solde' => $montantSolde,
            'epargne' => $montantEpargne,
        ];
    }


//promo
    public function promo_transfert(){
         $clientId = session()->get('client_id');
    if (!$clientId) {
        return redirect()->to('/login');
    }

    $numeroDestinataire = trim($this->request->getPost('numero_destinataire'));
    $montant = (float) $this->request->getPost('montant');
    $inclureFraisRetrait = (bool) $this->request->getPost('inclure_frais_retrait');

    if ($montant <= 0) {
        return redirect()->to('/transfert')->with('error', 'Montant invalide.');
    }

    $clientModel = new ClientModel();
    $client = $clientModel->find($clientId);

    if (!$client) {
        session()->destroy();
        return redirect()->to('/login');
    }
     if ($numeroDestinataire === $client['numero']) {
        return redirect()->to('/transfert')->with('error', 'Impossible de transférer vers votre propre numéro.');
    }

    $prefixeDestinataire = substr($numeroDestinataire, 0, 3);
    $idTypeTransfert = $this->getIdTypeOperation('transfert');

    // Determiner si le destinataire est chez NOTRE operateur
    $operateurModel = new OperateurModel();
    $operateurDestinataire = $operateurModel->where('code', $prefixeDestinataire)->first();

    $fraisTransfert = $this->calculerFrais($montant, $client['id_operateur'], $idTypeTransfert);
    $Promo= $montant * ($commission['pourcent_promo'] / 100);
    $fraisTransfertAvecPromo = $fraisTransfert + $Promo;

    if ($operateurDestinataire) {
        //si meme operateur que l'envoyeur
        $clientDestModel = new ClientModel();
        $destinataire = $clientDestModel->where('numero', $numeroDestinataire)->first();

        if (!$destinataire) {
            return redirect()->to('/transfert')->with('error', 'Ce numéro de destinataire n\'existe pas.');
        }

        //enlever juste le if sion veut que ca soit autom sans checkbox
        $fraisRetrait = 0;
        if ($inclureFraisRetrait) {
            $idTypeRetrait = $this->getIdTypeOperation('retrait');
            $fraisRetrait = $this->calculerFrais($montant, $destinataire['id_operateur'], $idTypeRetrait);


        }
//prendre le frais deja avec la promo
        $totalADeduire = $montant + $fraisTransfertAvecPromo + $fraisRetrait;

        if ($client['solde'] < $totalADeduire) {
            return redirect()->to('/transfert')->with('error', 'Solde insuffisant.');
        }
         $this->db = \Config\Database::connect();
        $this->db->transStart();

        // Debiter expediteur (montant + frais transfert + frais retrait anticipes)
        $clientModel->update($clientId, ['solde' => $client['solde'] - $totalADeduire]);

        // Crediter destinataire (montant + frais retrait anticipes si option cochee)
        $clientDestModel->update($destinataire['id'], [
            'solde' => $destinataire['solde'] + $montant + $fraisRetrait,
        ]);

        $operationModel = new \App\Models\OperationModel();
        $operationModel->insert([
            'id_client'          => $clientId,
            'destinataire_id'    => $destinataire['id'],
            'id_type_operation'  => $idTypeTransfert,
            'montant'            => $montant,
            'frais'              => $fraisTransfertAvecPromo,
            'frais_retrait'      => $fraisRetrait,
            'id_autre_operateur' => null,
            'date_operation'     => date('Y-m-d H:i:s'),
            
        ]);

        $this->db->transComplete();

        $messageMontant = $montant;

    } else {
        //si autre_operateur
        $autreOperateurModel = new \App\Models\AutreOperateurModel();
        $autreOperateur = $autreOperateurModel->where('code', $prefixeDestinataire)->first();

        if ($autreOperateur) {
            return redirect()->to('/transfert')->with('error', 'Impossible d envoyer de l argent au autres operateurs.');
        }
     
    if ($this->db->transStatus() === false) {
        return redirect()->to('/transfert')->with('error', 'Une erreur est survenue, réessaie.');
    }

    return redirect()->to('/dashboard')->with('success',
        'Transfert de ' . number_format($messageMontant, 0, ',', ' ') . ' Ar effectué.');
}

    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

}
