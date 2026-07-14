<?php

namespace App\Models;

use CodeIgniter\Model;

class AjuanModel extends Model
{
    protected $table      = 'tr_ajuan';
    protected $primaryKey = 'id_ajuan';
    protected $allowedFields = [
        'nomor_ajuan', 'nik', 'id_kategori_program', 'id_program', 'nilai_diajukan', 'deskripsi_ajuan',
        'jenis_ajuan', 'file_formulir', 'file_proposal', 'status_ajuan',
        'status_tersalurkan', 'nilai_disetujui', 'sifat_bantuan', 'edit_ajuan',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'tgl_diajukan';
    protected $updatedField  = 'last_updated_at';

    /**
     * Creates a new Ajuan with a unique 8-digit nomor_ajuan and the initial
     * "Diajukan" status, matching lazismu_reborn's registration flow.
     */
    public function simpan(array $data)
    {
        $noAjuan = (string) random_int(10000000, 99999999);
        while ($this->where('nomor_ajuan', $noAjuan)->countAllResults() > 0) {
            $noAjuan = (string) random_int(10000000, 99999999);
        }

        $data['nomor_ajuan']  = $noAjuan;
        $data['status_ajuan'] = $data['status_ajuan'] ?? 1;

        $this->transBegin();
        $this->insert($data);
        $ok = $this->transStatus() !== false;
        $ok ? $this->transCommit() : $this->transRollback();

        return $ok ? $noAjuan : false;
    }

    public function withRelasi()
    {
        return $this->select('
                tr_ajuan.*,
                tr_pemohon.nama_pemohon,
                tr_pemohon.telepon,
                ad_program.nama_program,
                dt_status_ajuan.keterangan_status
            ')
            ->join('tr_pemohon', 'tr_pemohon.nik = tr_ajuan.nik', 'left')
            ->join('ad_program', 'ad_program.id_program = tr_ajuan.id_program', 'left')
            ->join('dt_status_ajuan', 'dt_status_ajuan.id_status = tr_ajuan.status_ajuan', 'left');
    }
}
