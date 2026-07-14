<?php

namespace App\Controllers;

use App\Models\KategoriProgramModel;
use App\Models\PilarModel;

class PilarController extends BaseController
{
    protected PilarModel $pilarModel;

    public function __construct()
    {
        $this->pilarModel = new PilarModel();
    }

    public function index()
    {
        $data = [
            'title'      => 'Data Pilar',
            'activeMenu' => 'pilar',
            'pilar'      => $this->pilarModel->orderBy('id_pilar', 'ASC')->findAll(),
        ];

        return view('pilar/index', $data);
    }

    public function store()
    {
        $valid = [
            'nama_pilar' => ['label' => 'Nama pilar', 'rules' => 'required', 'errors' => ['required' => '{field} tidak boleh kosong']],
            'deskripsi_pilar' => ['label' => 'Deskripsi', 'rules' => 'required', 'errors' => ['required' => '{field} tidak boleh kosong']],
        ];

        if (!$this->validate($valid)) {
            session()->setFlashdata('gagal', implode(' ', $this->validator->getErrors()));
        } else {
            // dt_pilar.id_pilar is not auto-increment, so the next id is
            // computed here.
            $nextId = ((int) ($this->pilarModel->selectMax('id_pilar')->first()['id_pilar'] ?? 0)) + 1;

            $ok = $this->pilarModel->insert([
                'id_pilar'        => $nextId,
                'nama_pilar'      => $this->request->getPost('nama_pilar'),
                'deskripsi_pilar' => $this->request->getPost('deskripsi_pilar'),
            ]) !== false;

            session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Pilar berhasil disimpan!' : 'GAGAL menyimpan data!');
        }

        return redirect()->to(base_url('pilar'));
    }

    public function update(int $id)
    {
        $valid = [
            'nama_pilar' => ['label' => 'Nama pilar', 'rules' => 'required', 'errors' => ['required' => '{field} tidak boleh kosong']],
            'deskripsi_pilar' => ['label' => 'Deskripsi', 'rules' => 'required', 'errors' => ['required' => '{field} tidak boleh kosong']],
        ];

        if (!$this->validate($valid)) {
            session()->setFlashdata('gagal', implode(' ', $this->validator->getErrors()));
        } else {
            $ok = $this->pilarModel->update($id, [
                'nama_pilar'      => $this->request->getPost('nama_pilar'),
                'deskripsi_pilar' => $this->request->getPost('deskripsi_pilar'),
            ]);
            session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Pilar berhasil diperbarui!' : 'GAGAL menyimpan data!');
        }

        return redirect()->to(base_url('pilar'));
    }

    public function delete(int $id)
    {
        $dipakai = (new KategoriProgramModel())->where('id_pilar', $id)->countAllResults();

        if ($dipakai > 0) {
            session()->setFlashdata('gagal', 'Pilar tidak bisa dihapus karena masih digunakan oleh kategori program.');
        } else {
            $ok = $this->pilarModel->delete($id);
            session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Pilar berhasil dihapus!' : 'GAGAL menghapus data!');
        }

        return redirect()->to(base_url('pilar'));
    }
}
