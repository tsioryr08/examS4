<?php

namespace App\Controllers;

use App\Models\TypeOperationModel;

class TypeOperationController extends BaseController
{
    protected TypeOperationModel $model;

    public function __construct()
    {
        $this->model = new TypeOperationModel();
    }

    public function index()
    {
        return view('operateur/type_operation/index', [
            'types' => $this->model->findAll(),
        ]);
    }

    public function create()
    {
        return view('operateur/type_operation/form', ['type' => null]);
    }

    public function store()
    {
        $data = $this->request->getPost(['nom']);

        if (! $this->model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->model->errors());
        }

        return redirect()->to('/types-operation')->with('success', "Type d'opération créé.");
    }

    public function edit(int $id)
    {
        $type = $this->model->find($id);
        if (! $type) {
            return redirect()->to('/types-operation')->with('error', 'Type introuvable.');
        }

        return view('operateur/type_operation/form', ['type' => $type]);
    }

    public function update(int $id)
    {
        $data = $this->request->getPost(['nom']);
        $data['id'] = $id;

        if (! $this->model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->model->errors());
        }

        return redirect()->to('/types-operation')->with('success', 'Type modifié.');
    }

    public function delete(int $id)
    {
        $this->model->delete($id);
        return redirect()->to('/types-operation')->with('success', 'Type supprimé.');
    }
}