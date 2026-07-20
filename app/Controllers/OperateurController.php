<?php

namespace App\Controllers;

use App\Models\OperateurModel;

class OperateurController extends BaseController
{
    protected OperateurModel $model;

    public function __construct()
    {
        $this->model = new OperateurModel();
    }

    public function index()
    {
        return view('operateur/prefixe/index', [
            'operateurs' => $this->model->findAll(),
        ]);
    }

    public function create()
    {
        return view('operateur/prefixe/form', ['operateur' => null]);
    }

    public function store()
    {
        $data = $this->request->getPost(['libelle', 'code']);

        if (! $this->model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->model->errors());
        }

        return redirect()->to('/operateurs')->with('success', 'Opérateur créé.');
    }

    public function edit(int $id)
    {
        $operateur = $this->model->find($id);
        if (! $operateur) {
            return redirect()->to('/operateurs')->with('error', 'Opérateur introuvable.');
        }

        return view('operateur/prefixe/form', ['operateur' => $operateur]);
    }

    public function update(int $id)
    {
        $data = $this->request->getPost(['libelle', 'code']);
        $data['id'] = $id;

        if (! $this->model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->model->errors());
        }

        return redirect()->to('/operateurs')->with('success', 'Opérateur modifié.');
    }

    public function delete(int $id)
    {
        $this->model->delete($id);
        return redirect()->to('/operateurs')->with('success', 'Opérateur supprimé.');
    }
}