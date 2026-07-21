<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table            = 'client';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $allowedFields    = ['numero', 'nom', 'prenom', 'solde', 'id_operateur', 'pourcent_epargne','epargne'];

    protected $validationRules = [
        'id'            => 'permit_empty|integer',
        'numero'       => 'required|min_length[9]|is_unique[client.numero,id,{id}]',
        'id_operateur' => 'required|integer',
    ];

    public function getSituationComptes(?int $idOperateur = null)
    {
        $builder = $this->select('client.*, operateur.libelle as operateur_libelle, operateur.code as operateur_code')
            ->join('operateur', 'operateur.id = client.id_operateur');

        if ($idOperateur) {
            $builder->where('client.id_operateur', $idOperateur);
        }

        return $builder->orderBy('client.nom')->findAll();
    }

    public function crediter(int $idClient, float $montant): bool
    {
        return $this->set('solde', 'solde + ' . $montant, false)
            ->where('id', $idClient)
            ->update();
    }

    public function debiter(int $idClient, float $montant): bool
    {
        return $this->set('solde', 'solde - ' . $montant, false)
            ->where('id', $idClient)
            ->update();
    } 
    }
