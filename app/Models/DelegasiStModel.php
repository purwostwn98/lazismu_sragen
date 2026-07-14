<?php

namespace App\Models;

use CodeIgniter\Model;

class DelegasiStModel extends Model
{
    protected $table      = 'ad_delegasi_st';
    protected $primaryKey = 'id_delegasi';
    protected $allowedFields = ['id_surat_tugas', 'nama_delegasi'];
    public $timestamps = false;
}
