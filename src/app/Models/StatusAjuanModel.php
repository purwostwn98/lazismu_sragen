<?php

namespace App\Models;

use CodeIgniter\Model;

class StatusAjuanModel extends Model
{
    protected $table      = 'dt_status_ajuan';
    protected $primaryKey = 'id_status';
    protected $allowedFields = ['id_status', 'keterangan_status'];
    public $timestamps = false;
}
