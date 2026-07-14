<?php

namespace App\Controllers;

use App\Models\KategoriProgramModel;
use App\Models\PilarModel;
use App\Models\ProgramModel;

class KategoriProgramController extends BaseController
{
    protected KategoriProgramModel $kategoriModel;
    protected PilarModel $pilarModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriProgramModel();
        $this->pilarModel    = new PilarModel();
    }

    public function index()
    {
        $data = [
            'title'      => 'Data Kategori Program',
            'activeMenu' => 'kategori-program',
            'kategori'   => $this->kategoriModel
                ->select('ad_kategori_program.*, dt_pilar.nama_pilar')
                ->join('dt_pilar', 'dt_pilar.id_pilar = ad_kategori_program.id_pilar', 'left')
                ->orderBy('nama_kategori', 'ASC')
                ->findAll(),
            'pilar'      => $this->pilarModel->orderBy('nama_pilar', 'ASC')->findAll(),
        ];

        return view('kategori_program/index', $data);
    }

    private function rules(): array
    {
        return [
            'id_pilar'           => ['label' => 'Pilar', 'rules' => 'required', 'errors' => ['required' => '{field} wajib dipilih']],
            'nama_kategori'      => ['label' => 'Nama kategori', 'rules' => 'required', 'errors' => ['required' => '{field} tidak boleh kosong']],
            'deskripsi_kategori' => ['label' => 'Deskripsi', 'rules' => 'required', 'errors' => ['required' => '{field} tidak boleh kosong']],
            'status_kategori'    => ['label' => 'Status', 'rules' => 'required', 'errors' => ['required' => '{field} wajib dipilih']],
        ];
    }

    private function postData(): array
    {
        return [
            'id_pilar'           => $this->request->getPost('id_pilar'),
            'nama_kategori'      => $this->request->getPost('nama_kategori'),
            'deskripsi_kategori' => $this->request->getPost('deskripsi_kategori'),
            'status_kategori'    => $this->request->getPost('status_kategori'),
        ];
    }

    public function store()
    {
        if (!$this->validate($this->rules())) {
            session()->setFlashdata('gagal', implode(' ', $this->validator->getErrors()));
        } else {
            $ok = $this->kategoriModel->insert($this->postData()) !== false;
            session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Kategori program berhasil disimpan!' : 'GAGAL menyimpan data!');
        }

        return redirect()->to(base_url('kategori-program'));
    }

    public function update(int $id)
    {
        if (!$this->validate($this->rules())) {
            session()->setFlashdata('gagal', implode(' ', $this->validator->getErrors()));
        } else {
            $ok = $this->kategoriModel->update($id, $this->postData());
            session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Kategori program berhasil diperbarui!' : 'GAGAL menyimpan data!');
        }

        return redirect()->to(base_url('kategori-program'));
    }

    public function delete(int $id)
    {
        $dipakai = (new ProgramModel())->where('id_kategori_program', $id)->countAllResults();

        if ($dipakai > 0) {
            session()->setFlashdata('gagal', 'Kategori tidak bisa dihapus karena masih digunakan oleh kegiatan.');
        } else {
            $ok = $this->kategoriModel->delete($id);
            session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Kategori program berhasil dihapus!' : 'GAGAL menghapus data!');
        }

        return redirect()->to(base_url('kategori-program'));
    }
}
