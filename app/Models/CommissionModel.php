<?php

namespace App\Models;

use CodeIgniter\Model;

class CommissionModel extends Model
{
    protected $table      = 'commission';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_autre_operateur', 'id_type_operation', 'pourcent_commit'];

    public function getCommissionByOperateurAndType(int $idAutreOperateur, int $idTypeOperation): ?array
    {
        return $this->where('id_autre_operateur', $idAutreOperateur)
                    ->where('id_type_operation', $idTypeOperation)
                    ->first();
    }

    public function getCommissionsWithDetails()
    {
        return $this->select('commission.*, autre_operateur.libelle, autre_operateur.code, type_operation.nom')
                    ->join('autre_operateur', 'autre_operateur.id = commission.id_autre_operateur')
                    ->join('type_operation', 'type_operation.id = commission.id_type_operation')
                    ->findAll();
    }
}