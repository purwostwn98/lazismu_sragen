<?php

namespace App\Models;

use CodeIgniter\Model;

class SyaratModel extends Model
{
    protected $table      = 'ad_syarat_program';
    protected $primaryKey = 'id_syarat';
    protected $allowedFields = ['id_program', 'syarat_program'];
    public $timestamps = false;
}
