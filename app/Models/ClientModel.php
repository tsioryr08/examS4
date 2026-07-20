<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table = 'client';
    protected $primaryKey = 'id';
    protected $allowedFields = ['numero', 'nom', 'prenom', 'solde', 'id_operateur'];
    protected $useTimestamps = false;
}