<?php

namespace App\Controllers;

use App\Models\MuzakiModel;
use App\Models\PenghimpunanKtgModel;
use App\Models\PenghimpunanModel;
use App\Models\PenghimpunanSubktgModel;

class PenghimpunanController extends BaseController
{
    protected PenghimpunanModel $penghimpunanModel;
    protected PenghimpunanKtgModel $ktgModel;
    protected PenghimpunanSubktgModel $subktgModel;
    protected MuzakiModel $muzakiModel;

    public function __construct()
    {
        $this->penghimpunanModel = new PenghimpunanModel();
        $this->ktgModel          = new PenghimpunanKtgModel();
        $this->subktgModel       = new PenghimpunanSubktgModel();
        $this->muzakiModel       = new MuzakiModel();
    }

    public function index()
    {
        $data = [
            'title'      => 'Data Penghimpunan',
            'activeMenu' => 'penghimpunan',
            'penghimpunan' => $this->penghimpunanModel->withMuzaki()->orderBy('tanggal_himpun', 'DESC')->findAll(),
            'muzaki'     => $this->muzakiModel->orderBy('nama_muzaki', 'ASC')->findAll(),
            'ktg'        => $this->ktgModel->orderBy('keterangan_ktg', 'ASC')->findAll(),
            'subktg'     => $this->subktgModel->orderBy('keterangan_sub', 'ASC')->findAll(),
        ];

        return view('penghimpunan/index', $data);
    }

    public function store()
    {
        $valid = [
            'email_muzaki'   => ['label' => 'Muzaki', 'rules' => 'required', 'errors' => ['required' => '{field} wajib dipilih']],
            'tanggal_himpun' => ['label' => 'Tanggal', 'rules' => 'required', 'errors' => ['required' => '{field} wajib diisi']],
            'ktg_himpun'     => ['label' => 'Kategori', 'rules' => 'required', 'errors' => ['required' => '{field} wajib dipilih']],
            'jumlah_himpun'  => ['label' => 'Jumlah', 'rules' => 'required|numeric', 'errors' => ['required' => '{field} wajib diisi', 'numeric' => '{field} harus berupa angka']],
            'via_himpun'     => ['label' => 'Metode', 'rules' => 'required', 'errors' => ['required' => '{field} wajib dipilih']],
        ];

        if (!$this->validate($valid)) {
            session()->setFlashdata('gagal', implode(' ', $this->validator->getErrors()));
        } else {
            $ok = $this->penghimpunanModel->simpan(
                $this->request->getPost('email_muzaki'),
                $this->request->getPost('tanggal_himpun'),
                $this->request->getPost('ktg_himpun'),
                $this->request->getPost('sub_ktg_himpun'),
                $this->request->getPost('jumlah_himpun'),
                $this->request->getPost('via_himpun'),
                $this->request->getPost('tgl_setor_bank') ?: null,
                $this->request->getPost('kwitansi_bank'),
                $this->request->getPost('nm_bank')
            );
            session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Data penghimpunan berhasil tersimpan!' : 'GAGAL menyimpan data!');
        }

        return redirect()->to(base_url('penghimpunan'));
    }

    public function delete(string $id)
    {
        $ok = $this->penghimpunanModel->hapus($id);
        session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Data penghimpunan berhasil dihapus!' : 'GAGAL menghapus data!');

        return redirect()->to(base_url('penghimpunan'));
    }
}
