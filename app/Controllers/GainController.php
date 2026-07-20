<?php

namespace App\Controllers;

use App\Models\OperateurModel;
use App\Models\OperationModel;

class GainController extends BaseController
{
    public function index()
    {
        $operationModel = new OperationModel();
        $operateurModel = new OperateurModel();

        $dateDebut   = $this->request->getGet('date_debut');
        $dateFin     = $this->request->getGet('date_fin');
        $idOperateur = $this->request->getGet('id_operateur') ?: null;

        return view('operateur/gain/index', [
            'gains'       => $operationModel->getGainsParType($dateDebut, $dateFin, $idOperateur),
            'total'       => $operationModel->getGainTotal($dateDebut, $dateFin, $idOperateur),
            'operateurs'  => $operateurModel->findAll(),
            'dateDebut'   => $dateDebut,
            'dateFin'     => $dateFin,
            'idOperateur' => $idOperateur,
        ]);
    }
}