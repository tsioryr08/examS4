<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\OperateurModel;

class ClientCompteController extends BaseController
{
    public function index()
    {
        $clientModel    = new ClientModel();
        $operateurModel = new OperateurModel();

        $idOperateur = $this->request->getGet('id_operateur') ?: null;
        $clients     = $clientModel->getSituationComptes($idOperateur);

        return view('operateur/client_compte/index', [
            'clients'     => $clients,
            'operateurs'  => $operateurModel->findAll(),
            'idOperateur' => $idOperateur,
            'soldeTotal'  => array_sum(array_column($clients, 'solde')),
        ]);
    }
}