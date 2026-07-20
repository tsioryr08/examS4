<?php

namespace App\Models;

use CodeIgniter\Model;

class OperationModel extends Model
{
    protected $table = 'operation';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_client', 'destinataire_id', 'id_type_operation',
        'montant', 'frais', 'date_operation'
    ];
    protected $useTimestamps = false;
}