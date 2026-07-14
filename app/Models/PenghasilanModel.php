<?php

namespace App\Models;

use CodeIgniter\Model;

class PenghasilanModel extends Model
{
    protected $table      = 'dt_penghasilan';
    protected $primaryKey = 'id_penghasilan';
    protected $allowedFields = ['label_penghasilan'];
    public $timestamps = false;
}
