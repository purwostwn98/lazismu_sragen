<?php

namespace App\Models;

use CodeIgniter\Model;

class PilarModel extends Model
{
    protected $table      = 'dt_pilar';
    protected $primaryKey = 'id_pilar';
    protected $allowedFields = ['id_pilar', 'nama_pilar', 'deskripsi_pilar'];
    public $timestamps = false;
}
