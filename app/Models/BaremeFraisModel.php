<?php

namespace App\Models;

use CodeIgniter\Model;

class BaremeFraisModel extends Model
{
    protected $table = 'bareme_frais';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_operateur', 'id_type_operation', 'montant_min', 'montant_max', 'frais'];
    protected $useTimestamps = false;
}