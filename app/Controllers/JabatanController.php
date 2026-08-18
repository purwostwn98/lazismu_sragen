<?php

namespace App\Controllers;

use App\Models\JabatanModel;
use App\Models\JabatanPenjabatModel;

class JabatanController extends BaseController
{
    protected JabatanModel $jabatanModel;

    public function __construct()
    {
        $this->jabatanModel = new JabatanModel();
    }

    public function index()
    {
        $data = [
            'title'      => 'Data Jabatan',
            'activeMenu' => 'jabatan',
            'jabatan'    => $this->jabatanModel->orderBy('nama_jabatan', 'ASC')->findAll(),
        ];

        return view('jabatan/index', $data);
    }

    private function rules(?int $id = null): array
    {
        $unique = 'is_unique[jabatan.kode_jabatan' . ($id !== null ? ',id,' . $id : '') . ']';

        return [
            'kode_jabatan' => [
                'label'  => 'Kode jabatan',
                'rules'  => 'required|' . $unique,
                'errors' => ['required' => '{field} tidak boleh kosong', 'is_unique' => 'Kode jabatan sudah digunakan'],
            ],
            'nama_jabatan' => [
                'label'  => 'Nama jabatan',
                'rules'  => 'required',
                'errors' => ['required' => '{field} tidak boleh kosong'],
            ],
        ];
    }

    private function postData(): array
    {
        return [
            'kode_jabatan' => $this->request->getPost('kode_jabatan'),
            'nama_jabatan' => $this->request->getPost('nama_jabatan'),
        ];
    }

    public function store()
    {
        if (!$this->validate($this->rules())) {
            session()->setFlashdata('gagal', implode(' ', $this->validator->getErrors()));
        } else {
            $ok = $this->jabatanModel->insert($this->postData()) !== false;
            session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Jabatan berhasil disimpan!' : 'GAGAL menyimpan data!');
        }

        return redirect()->to(base_url('jabatan'));
    }

    public function update(int $id)
    {
        if (!$this->validate($this->rules($id))) {
            session()->setFlashdata('gagal', implode(' ', $this->validator->getErrors()));
        } else {
            $ok = $this->jabatanModel->update($id, $this->postData());
            session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Jabatan berhasil diperbarui!' : 'GAGAL menyimpan data!');
        }

        return redirect()->to(base_url('jabatan'));
    }

    public function delete(int $id)
    {
        $dipakai = (new JabatanPenjabatModel())->where('id_jabatan', $id)->countAllResults();

        if ($dipakai > 0) {
            session()->setFlashdata('gagal', 'Jabatan tidak bisa dihapus karena masih memiliki data penjabat.');
        } else {
            $ok = $this->jabatanModel->delete($id);
            session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Jabatan berhasil dihapus!' : 'GAGAL menghapus data!');
        }

        return redirect()->to(base_url('jabatan'));
    }
}
