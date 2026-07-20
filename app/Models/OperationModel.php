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
    protected $allowedFields = [
    'id_client', 'destinataire_id', 'id_type_operation',
    'montant', 'frais', 'frais_retrait', 'id_autre_operateur', 'date_operation',
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

    /**
     * Gains propres (frais de retrait et transfert interne)
     */
    public function getGainsParTypePropre(?string $dateDebut = null, ?string $dateFin = null, ?int $idOperateur = null)
    {
        $builder = $this->select('type_operation.nom as type_nom, SUM(operation.frais) as total_frais, COUNT(operation.id) as nb_operations')
            ->join('type_operation', 'type_operation.id = operation.id_type_operation')
            ->join('client', 'client.id = operation.id_client')
            ->where('operation.id_autre_operateur', NULL);

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

    /**
     * Gains des commissions inter-opérateurs
     */
    public function getGainsCommissionInterOperateur(?string $dateDebut = null, ?string $dateFin = null, ?int $idOperateur = null)
    {
        $builder = $this->select('autre_operateur.libelle, autre_operateur.code, type_operation.nom as type_nom, 
                                  SUM(operation.montant) as montant_total, 
                                  commission.pourcent_commit, 
                                  SUM(operation.montant * commission.pourcent_commit / 100) as commission_montant')
            ->join('type_operation', 'type_operation.id = operation.id_type_operation')
            ->join('client', 'client.id = operation.id_client')
            ->join('autre_operateur', 'autre_operateur.id = operation.id_autre_operateur')
            ->join('commission', 'commission.id_autre_operateur = autre_operateur.id AND commission.id_type_operation = operation.id_type_operation')
            ->where('operation.id_autre_operateur IS NOT NULL');

        if ($dateDebut) {
            $builder->where('operation.date_operation >=', $dateDebut);
        }
        if ($dateFin) {
            $builder->where('operation.date_operation <=', $dateFin);
        }
        if ($idOperateur) {
            $builder->where('client.id_operateur', $idOperateur);
        }

        return $builder->groupBy('operation.id_autre_operateur')->findAll();
    }

    /**
     * Montants à envoyer à chaque autre opérateur
     */
    public function getMontantsAEnvoyer(?string $dateDebut = null, ?string $dateFin = null, ?int $idOperateur = null)
    {
        $builder = $this->select('autre_operateur.id, autre_operateur.libelle, autre_operateur.code, 
                                  SUM(operation.montant) as montant_total')
            ->join('client', 'client.id = operation.id_client')
            ->join('autre_operateur', 'autre_operateur.id = operation.id_autre_operateur')
            ->where('operation.id_autre_operateur IS NOT NULL');

        if ($dateDebut) {
            $builder->where('operation.date_operation >=', $dateDebut);
        }
        if ($dateFin) {
            $builder->where('operation.date_operation <=', $dateFin);
        }
        if ($idOperateur) {
            $builder->where('client.id_operateur', $idOperateur);
        }

        return $builder->groupBy('operation.id_autre_operateur')->findAll();
    }
}