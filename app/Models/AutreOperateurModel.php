<?php

namespace App\Models;

use CodeIgniter\Model;

class AutreOperateurModel extends Model
{
    protected $table      = 'autre_operateur';
    protected $primaryKey = 'id';
    protected $allowedFields = ['libelle', 'code'];

    public function getAll()
    {
        return $this->findAll();
    }
}