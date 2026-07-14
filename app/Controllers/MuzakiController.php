<?php

namespace App\Controllers;

use App\Models\MuzakiModel;

class MuzakiController extends BaseController
{
    protected MuzakiModel $muzakiModel;

    public function __construct()
    {
        $this->muzakiModel = new MuzakiModel();
    }

    public function index()
    {
        $data = [
            'title'      => 'Data Muzaki',
            'activeMenu' => 'muzaki',
            'muzaki'     => $this->muzakiModel->orderBy('nama_muzaki', 'ASC')->findAll(),
        ];

        return view('muzaki/index', $data);
    }

    public function store()
    {
        $valid = [
            'nama_muzaki' => [
                'label'  => 'Nama Muzaki',
                'rules'  => 'required',
                'errors' => ['required' => '{field} tidak boleh kosong'],
            ],
            'email_muzaki' => [
                'label'  => 'Email muzaki',
                'rules'  => 'required|valid_email|is_unique[dt_muzaki.email_muzaki]',
                'errors' => [
                    'required'   => '{field} tidak boleh kosong',
                    'is_unique'  => 'Gagal, {field} sudah pernah digunakan',
                ],
            ],
        ];

        if (!$this->validate($valid)) {
            session()->setFlashdata('gagal', $this->validator->getError('email_muzaki') ?: $this->validator->getError('nama_muzaki'));
        } else {
            $ok = $this->muzakiModel->simpan(
                $this->request->getPost('nama_muzaki'),
                $this->request->getPost('alamat_muzaki'),
                $this->request->getPost('tlp_muzaki'),
                $this->request->getPost('email_muzaki'),
                $this->request->getPost('jenis_muzaki'),
                $this->request->getPost('is_dosen') ? 1 : 0
            );
            session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Data muzaki berhasil tersimpan!' : 'GAGAL menyimpan data!');
        }

        return redirect()->to(base_url('muzaki'));
    }

    public function update(string $id)
    {
        $valid = [
            'nama_muzaki' => [
                'label'  => 'Nama Muzaki',
                'rules'  => 'required',
                'errors' => ['required' => '{field} tidak boleh kosong'],
            ],
            'email_muzaki' => [
                'label'  => 'Email muzaki',
                'rules'  => 'required|valid_email|is_unique[dt_muzaki.email_muzaki,id_muzaki,' . $id . ']',
                'errors' => [
                    'required'  => '{field} tidak boleh kosong',
                    'is_unique' => 'Gagal, {field} sudah pernah digunakan',
                ],
            ],
        ];

        if (!$this->validate($valid)) {
            session()->setFlashdata('gagal', $this->validator->getError('email_muzaki') ?: $this->validator->getError('nama_muzaki'));
        } else {
            $ok = $this->muzakiModel->perbarui(
                $id,
                $this->request->getPost('nama_muzaki'),
                $this->request->getPost('alamat_muzaki'),
                $this->request->getPost('tlp_muzaki'),
                $this->request->getPost('email_muzaki'),
                $this->request->getPost('jenis_muzaki'),
                $this->request->getPost('is_dosen') ? 1 : 0
            );
            session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Data muzaki berhasil diperbarui!' : 'GAGAL menyimpan data!');
        }

        return redirect()->to(base_url('muzaki'));
    }

    public function delete(string $id)
    {
        $ok = $this->muzakiModel->delete($id);
        session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Data muzaki berhasil dihapus!' : 'GAGAL menghapus data!');

        return redirect()->to(base_url('muzaki'));
    }
}
