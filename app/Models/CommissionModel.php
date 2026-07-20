<?php

namespace App\Models;

use CodeIgniter\Model;

class CommissionModel extends Model
{
    protected $table = 'commission';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_autre_operateur', 'pourcent_commit', 'id_type_operation'];
    protected $useTimestamps = false;
}