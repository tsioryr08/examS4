<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeOperationModel extends Model
{
    protected $table            = 'type_operation';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $allowedFields    = ['nom'];

    protected $validationRules = [
        'nom' => 'required|min_length[3]|max_length[50]|is_unique[type_operation.nom,id,{id}]',
    ];
}