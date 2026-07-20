<?php

namespace App\Controllers;

use App\Models\UtilisateurModel;

class AuthController extends BaseController
{
    public function login()
    {
        return view('auth/login');
    }

    public function verifier()
    {
        $numero_tel      = $this->request->getPost('numero');

        $model       = new ClientModel();
        $utilisateur = $model->where('NumeroTel', $numero_tel)->first();

        if ($utilisateur) {
            session()->set('utilisateur_id', $utilisateur['id']);
            session()->set('utilisateur_nom', $utilisateur['Nom']);

            return redirect()->to('/caisse/choix');
        }

        // return redirect()->to('/login')->with('error', 'Email ou mot de passe incorrect.');
        return redirect()->to('/login')->with('error', 'Identifiants incorrects.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}