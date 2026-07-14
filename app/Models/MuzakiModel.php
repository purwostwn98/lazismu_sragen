<?php

namespace App\Models;

use CodeIgniter\Model;

class MuzakiModel extends Model
{
    protected $table      = 'dt_muzaki';
    protected $primaryKey = 'id_muzaki';
    protected $allowedFields = [
        'id_muzaki', 'nama_muzaki', 'alamat_muzaki', 'tlp_muzaki',
        'email_muzaki', 'jenis_muzaki', 'is_dosen',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'mzk_crat';
    protected $updatedField  = 'mzk_uat';

    public function __construct()
    {
        parent::__construct();
        helper('fungsi');
    }

    public function simpan($nama_muzaki, $alamat_muzaki, $tlp_muzaki, $email_muzaki, $jenis_muzaki, $is_dosen = 0)
    {
        $firstString  = getFirstLetters($nama_muzaki);
        $id           = 'mzi-' . strtolower($firstString) . getRandomNumber001_009();

        while ($this->where('id_muzaki', $id)->countAllResults() > 0) {
            $id = 'mzi-' . strtolower($firstString) . getRandomNumber001_009();
        }

        $data = [
            'id_muzaki'     => $id,
            'nama_muzaki'   => $nama_muzaki,
            'alamat_muzaki' => $alamat_muzaki,
            'tlp_muzaki'    => $tlp_muzaki,
            'email_muzaki'  => $email_muzaki,
            'jenis_muzaki'  => $jenis_muzaki,
            'is_dosen'      => $is_dosen,
        ];

        $this->transBegin();
        $this->insert($data);
        $ok = $this->transStatus() !== false;
        $ok ? $this->transCommit() : $this->transRollback();

        return $ok;
    }

    public function perbarui($id_muzaki, $nama_muzaki, $alamat_muzaki, $tlp_muzaki, $email_muzaki, $jenis_muzaki, $is_dosen)
    {
        $data = [
            'nama_muzaki'   => $nama_muzaki,
            'alamat_muzaki' => $alamat_muzaki,
            'tlp_muzaki'    => $tlp_muzaki,
            'email_muzaki'  => $email_muzaki,
            'jenis_muzaki'  => $jenis_muzaki,
            'is_dosen'      => $is_dosen,
        ];

        $this->transBegin();
        $this->update($id_muzaki, $data);
        $ok = $this->transStatus() !== false;
        $ok ? $this->transCommit() : $this->transRollback();

        return $ok;
    }
}
