<?php

namespace App\Models;

use CodeIgniter\Model;

class OperateurModel extends Model
{
    protected $table            = 'operateur';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $allowedFields    = ['libelle', 'code'];

    protected $validationRules = [
        'id'      => 'permit_empty|integer',
        'libelle' => 'required|min_length[2]|max_length[100]',
        'code'    => 'required|min_length[2]|max_length[10]|is_unique[operateur.code,id,{id}]',
    ];

    protected $validationMessages = [
        'code' => [
            'is_unique' => 'Ce préfixe existe déjà pour un autre opérateur.',
        ],
    ];
}
