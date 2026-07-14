<?php

namespace App\Models;

use CodeIgniter\Model;

class DokumentasiModel extends Model
{
    protected $table      = 'tr_dokumentasi';
    protected $primaryKey = 'id_dokumentasi';
    protected $allowedFields = ['no_ajuan', 'nama_file', 'status', 'catatan'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function simpan($no_ajuan, $nama_file, $status, $catatan = '')
    {
        $this->transBegin();
        $this->insert([
            'no_ajuan'  => $no_ajuan,
            'nama_file' => $nama_file,
            'status'    => $status,
            'catatan'   => $catatan,
        ]);
        $ok = $this->transStatus() !== false;
        $ok ? $this->transCommit() : $this->transRollback();

        return $ok;
    }
}
