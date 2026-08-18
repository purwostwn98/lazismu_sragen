<?php

namespace App\Controllers;

use App\Models\JabatanModel;
use App\Models\JabatanPenjabatModel;

class PenjabatController extends BaseController
{
    protected JabatanPenjabatModel $penjabatModel;
    protected JabatanModel $jabatanModel;

    public function __construct()
    {
        $this->penjabatModel = new JabatanPenjabatModel();
        $this->jabatanModel  = new JabatanModel();
    }

    public function index()
    {
        $data = [
            'title'      => 'Data Penjabat',
            'activeMenu' => 'penjabat',
            'penjabat'   => $this->penjabatModel->orderBy('mulai_tahun', 'DESC')->orderBy('nama_penjabat', 'ASC')->findAll(),
            'jabatan'    => $this->jabatanModel->orderBy('nama_jabatan', 'ASC')->findAll(),
        ];

        return view('penjabat/index', $data);
    }

    private function rules(): array
    {
        return [
            'id_jabatan' => [
                'label'  => 'Jabatan',
                'rules'  => 'required',
                'errors' => ['required' => '{field} wajib dipilih'],
            ],
            'nama_penjabat' => [
                'label'  => 'Nama penjabat',
                'rules'  => 'required',
                'errors' => ['required' => '{field} tidak boleh kosong'],
            ],
            'email' => [
                'label'  => 'Email',
                'rules'  => 'permit_empty|valid_email',
                'errors' => ['valid_email' => '{field} tidak valid'],
            ],
            'mulai_tahun' => [
                'label'  => 'Mulai tahun',
                'rules'  => 'permit_empty|numeric',
                'errors' => ['numeric' => '{field} harus angka'],
            ],
        ];
    }

    private function postData(): array
    {
        $idJabatan = (int) $this->request->getPost('id_jabatan');
        $jabatan   = $this->jabatanModel->find($idJabatan);

        return [
            'id_jabatan'    => $idJabatan,
            'nama_jabatan'  => $jabatan['nama_jabatan'] ?? '',
            'nama_penjabat' => $this->request->getPost('nama_penjabat'),
            'email'         => $this->request->getPost('email') ?: null,
            'mulai_tahun'   => $this->request->getPost('mulai_tahun') ?: null,
        ];
    }

    public function store()
    {
        if (!$this->validate($this->rules())) {
            session()->setFlashdata('gagal', implode(' ', $this->validator->getErrors()));
        } else {
            $ok = $this->penjabatModel->insert($this->postData()) !== false;
            session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Penjabat berhasil disimpan!' : 'GAGAL menyimpan data!');
        }

        return redirect()->to(base_url('penjabat'));
    }

    public function update(int $id)
    {
        if (!$this->validate($this->rules())) {
            session()->setFlashdata('gagal', implode(' ', $this->validator->getErrors()));
        } else {
            $ok = $this->penjabatModel->update($id, $this->postData());
            session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Penjabat berhasil diperbarui!' : 'GAGAL menyimpan data!');
        }

        return redirect()->to(base_url('penjabat'));
    }

    public function delete(int $id)
    {
        $ok = $this->penjabatModel->delete($id);
        session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Penjabat berhasil dihapus!' : 'GAGAL menghapus data!');

        return redirect()->to(base_url('penjabat'));
    }
}
