<?php

namespace App\Controllers;

use App\Models\BaremeFraisModel;
use App\Models\OperateurModel;
use App\Models\TypeOperationModel;

class BaremeFraisController extends BaseController
{
    protected BaremeFraisModel $model;
    protected OperateurModel $operateurModel;
    protected TypeOperationModel $typeModel;

    public function __construct()
    {
        $this->model          = new BaremeFraisModel();
        $this->operateurModel = new OperateurModel();
        $this->typeModel      = new TypeOperationModel();
    }

    public function index()
{
    $idOperateur = $this->request->getGet('id_operateur');

    return view('operateur/bareme_frais/index', [
        'groupes'    => $this->model->getBaremesGroupes($idOperateur ?: null),
        'operateurs' => $this->operateurModel->findAll(),
    ]);
}

    public function create()
    {
        return view('operateur/bareme_frais/form', [
            'bareme'     => null,
            'operateurs' => $this->operateurModel->findAll(),
            'types'      => $this->typeModel->findAll(),
        ]);
    }

    public function store()
    {
        $data = $this->request->getPost(['id_operateur', 'id_type_operation', 'montant_min', 'montant_max', 'frais']);

        if ((float) $data['montant_min'] >= (float) $data['montant_max']) {
            return redirect()->back()->withInput()->with('error', 'Le montant min doit être inférieur au montant max.');
        }

        if ($this->model->tranchesChevauchent((int) $data['id_operateur'], (int) $data['id_type_operation'], (float) $data['montant_min'], (float) $data['montant_max'])) {
            return redirect()->back()->withInput()->with('error', 'Cette tranche chevauche une tranche existante pour cet opérateur/type.');
        }

        if (! $this->model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->model->errors());
        }

        return redirect()->to('/baremes')->with('success', 'Tranche de frais créée.');
    }

    public function edit(int $id)
    {
        $bareme = $this->model->find($id);
        if (! $bareme) {
            return redirect()->to('/baremes')->with('error', 'Tranche introuvable.');
        }

        return view('operateur/bareme_frais/form', [
            'bareme'     => $bareme,
            'operateurs' => $this->operateurModel->findAll(),
            'types'      => $this->typeModel->findAll(),
        ]);
    }

    public function update(int $id)
    {
        $data = $this->request->getPost(['id_operateur', 'id_type_operation', 'montant_min', 'montant_max', 'frais']);

        if ((float) $data['montant_min'] >= (float) $data['montant_max']) {
            return redirect()->back()->withInput()->with('error', 'Le montant min doit être inférieur au montant max.');
        }

        if ($this->model->tranchesChevauchent((int) $data['id_operateur'], (int) $data['id_type_operation'], (float) $data['montant_min'], (float) $data['montant_max'], $id)) {
            return redirect()->back()->withInput()->with('error', 'Cette tranche chevauche une tranche existante pour cet opérateur/type.');
        }

        $data['id'] = $id;

        if (! $this->model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->model->errors());
        }

        return redirect()->to('/baremes')->with('success', 'Tranche modifiée.');
    }

    public function delete(int $id)
    {
        $this->model->delete($id);
        return redirect()->to('/baremes')->with('success', 'Tranche supprimée.');
    }
}