<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterLembagaModel extends Model
{
    protected $table      = 'ms_lembaga';
    protected $primaryKey = 'id_ms_lembaga';
    protected $allowedFields = [
        'nama_lembaga', 'bidang', 'tahun_berdiri', 'nomor_legalitas', 'npwp', 'alamat',
        'nomor_telepon', 'email', 'website', 'nama_pj', 'jabatan_pj', 'sumber_pendanaan', 'nomor_rekening',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /** Insert a new master record, or refresh an existing one by nomor_legalitas. */
    public function upsert(array $data): void
    {
        $nomor    = strtoupper(trim($data['nomor_legalitas']));
        $existing = $this->where('nomor_legalitas', $nomor)->first();

        $data['nomor_legalitas'] = $nomor;

        if ($existing) {
            $this->update($existing['id_ms_lembaga'], $data);
        } else {
            $this->insert($data);
        }
    }
}
