<?php

namespace App\Models;

use CodeIgniter\Model;

class OperationModel extends Model
{
    protected $table            = 'operation';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $allowedFields    = [
        'id_client', 'destinataire_id', 'id_type_operation', 'montant', 'frais', 'date_operation',
    ];

    public function getGainsParType(?string $dateDebut = null, ?string $dateFin = null, ?int $idOperateur = null)
    {
        $builder = $this->select('type_operation.nom as type_nom, SUM(operation.frais) as total_frais, COUNT(operation.id) as nb_operations')
            ->join('type_operation', 'type_operation.id = operation.id_type_operation')
            ->join('client', 'client.id = operation.id_client');

        if ($dateDebut) {
            $builder->where('operation.date_operation >=', $dateDebut);
        }
        if ($dateFin) {
            $builder->where('operation.date_operation <=', $dateFin);
        }
        if ($idOperateur) {
            $builder->where('client.id_operateur', $idOperateur);
        }

        return $builder->groupBy('operation.id_type_operation')->findAll();
    }

    public function getGainTotal(?string $dateDebut = null, ?string $dateFin = null, ?int $idOperateur = null): float
    {
        $builder = $this->select('SUM(operation.frais) as total')
            ->join('client', 'client.id = operation.id_client');

        if ($dateDebut) {
            $builder->where('operation.date_operation >=', $dateDebut);
        }
        if ($dateFin) {
            $builder->where('operation.date_operation <=', $dateFin);
        }
        if ($idOperateur) {
            $builder->where('client.id_operateur', $idOperateur);
        }

        $result = $builder->first();
        return (float) ($result['total'] ?? 0);
    }
}