<?php

namespace App\Models;

use CodeIgniter\Model;

class ProgramModel extends Model
{
    protected $table      = 'ad_program';
    protected $primaryKey = 'id_program';
    protected $allowedFields = [
        'id_kategori_program', 'nama_program', 'deskripsi_program', 'jenis_formulir', 'status_program',
    ];
    public $timestamps = false;

    public function withKategori()
    {
        return $this->select('ad_program.*, ad_kategori_program.nama_kategori, dt_pilar.nama_pilar')
            ->join('ad_kategori_program', 'ad_kategori_program.id_kategori_program = ad_program.id_kategori_program', 'left')
            ->join('dt_pilar', 'dt_pilar.id_pilar = ad_kategori_program.id_pilar', 'left');
    }
}
