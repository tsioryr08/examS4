<?php

namespace App\Controllers;

use App\Models\OperateurModel;
use App\Models\OperationModel;
use App\Models\AutreOperateurModel;

class GainController extends BaseController
{
    public function index()
    {
        $operationModel = new OperationModel();
        $operateurModel = new OperateurModel();
        $autreOperateurModel = new AutreOperateurModel();

        $dateDebut   = $this->request->getGet('date_debut');
        $dateFin     = $this->request->getGet('date_fin');
        $idOperateur = $this->request->getGet('id_operateur') ?: null;

        return view('operateur/gain/index', [
            'gains'                      => $operationModel->getGainsParType($dateDebut, $dateFin, $idOperateur),
            'gainsPropres'               => $operationModel->getGainsParTypePropre($dateDebut, $dateFin, $idOperateur),
            'gainsCommissionInterOp'     => $operationModel->getGainsCommissionInterOperateur($dateDebut, $dateFin, $idOperateur),
            'montantsAEnvoyer'           => $operationModel->getMontantsAEnvoyer($dateDebut, $dateFin, $idOperateur),
            'total'                      => $operationModel->getGainTotal($dateDebut, $dateFin, $idOperateur),
            'operateurs'                 => $operateurModel->findAll(),
            'autresOperateurs'           => $autreOperateurModel->getAll(),
            'dateDebut'                  => $dateDebut,
            'dateFin'                    => $dateFin,
            'idOperateur'                => $idOperateur,
        ]);
    }
}
