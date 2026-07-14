<?php

namespace App\Controllers;

use App\Models\AjuanModel;
use App\Models\KategoriProgramModel;
use App\Models\ProgramModel;
use App\Models\SyaratModel;

class ProgramController extends BaseController
{
    protected ProgramModel $programModel;
    protected KategoriProgramModel $kategoriModel;
    protected SyaratModel $syaratModel;

    public function __construct()
    {
        $this->programModel  = new ProgramModel();
        $this->kategoriModel = new KategoriProgramModel();
        $this->syaratModel   = new SyaratModel();
    }

    public function index()
    {
        $syaratByProgram = [];
        foreach ($this->syaratModel->orderBy('id_syarat', 'ASC')->findAll() as $sy) {
            $syaratByProgram[$sy['id_program']][] = $sy;
        }

        $data = [
            'title'      => 'Data Kegiatan',
            'activeMenu' => 'program',
            'program'    => $this->programModel->withKategori()->orderBy('nama_program', 'ASC')->findAll(),
            'kategori'   => $this->kategoriModel->orderBy('nama_kategori', 'ASC')->findAll(),
            'jenisFormulir'    => $this->jenisFormulirOptions(),
            'syaratByProgram'  => $syaratByProgram,
        ];

        return view('program/index', $data);
    }

    private function simpanSyarat(int $idProgram): void
    {
        foreach ((array) $this->request->getPost('syarat') as $syarat) {
            $syarat = trim((string) $syarat);
            if ($syarat !== '') {
                $this->syaratModel->insert(['id_program' => $idProgram, 'syarat_program' => $syarat]);
            }
        }
    }

    public function store()
    {
        $valid = [
            'id_kategori_program' => ['label' => 'Kategori', 'rules' => 'required', 'errors' => ['required' => '{field} wajib dipilih']],
            'nama_program'        => ['label' => 'Nama kegiatan', 'rules' => 'required', 'errors' => ['required' => '{field} tidak boleh kosong']],
            'jenis_formulir'      => ['label' => 'Jenis formulir', 'rules' => 'required', 'errors' => ['required' => '{field} wajib dipilih']],
            'status_program'      => ['label' => 'Status kegiatan', 'rules' => 'required', 'errors' => ['required' => '{field} wajib dipilih']],
        ];

        if (!$this->validate($valid)) {
            session()->setFlashdata('gagal', implode(' ', $this->validator->getErrors()));
        } else {
            $ok = $this->programModel->insert([
                'id_kategori_program' => $this->request->getPost('id_kategori_program'),
                'nama_program'        => $this->request->getPost('nama_program'),
                'deskripsi_program'   => $this->request->getPost('deskripsi_program'),
                'jenis_formulir'      => $this->request->getPost('jenis_formulir'),
                'status_program'      => $this->request->getPost('status_program'),
            ]);

            if ($ok) {
                $this->simpanSyarat((int) $ok);
            }

            session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Kegiatan berhasil disimpan!' : 'GAGAL menyimpan data!');
        }

        return redirect()->to(base_url('program'));
    }

    public function update(int $id)
    {
        $valid = [
            'id_kategori_program' => ['label' => 'Kategori', 'rules' => 'required', 'errors' => ['required' => '{field} wajib dipilih']],
            'nama_program'        => ['label' => 'Nama kegiatan', 'rules' => 'required', 'errors' => ['required' => '{field} tidak boleh kosong']],
            'jenis_formulir'      => ['label' => 'Jenis formulir', 'rules' => 'required', 'errors' => ['required' => '{field} wajib dipilih']],
            'status_program'      => ['label' => 'Status kegiatan', 'rules' => 'required', 'errors' => ['required' => '{field} wajib dipilih']],
        ];

        if (!$this->validate($valid)) {
            session()->setFlashdata('gagal', implode(' ', $this->validator->getErrors()));
        } else {
            $ok = $this->programModel->update($id, [
                'id_kategori_program' => $this->request->getPost('id_kategori_program'),
                'nama_program'        => $this->request->getPost('nama_program'),
                'deskripsi_program'   => $this->request->getPost('deskripsi_program'),
                'jenis_formulir'      => $this->request->getPost('jenis_formulir'),
                'status_program'      => $this->request->getPost('status_program'),
            ]);

            if ($ok) {
                $this->syaratModel->where('id_program', $id)->delete();
                $this->simpanSyarat($id);
            }

            session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Kegiatan berhasil diperbarui!' : 'GAGAL menyimpan data!');
        }

        return redirect()->to(base_url('program'));
    }

    public function delete(int $id)
    {
        $dipakai = (new AjuanModel())->where('id_program', $id)->countAllResults();

        if ($dipakai > 0) {
            session()->setFlashdata('gagal', 'Kegiatan tidak bisa dihapus karena sudah digunakan pada pengajuan. Nonaktifkan status kegiatan jika sudah tidak berlaku.');
        } else {
            $ok = $this->programModel->delete($id);
            session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Kegiatan berhasil dihapus!' : 'GAGAL menghapus data!');
        }

        return redirect()->to(base_url('program'));
    }

    private function jenisFormulirOptions(): array
    {
        return [
            0 => 'Lainnya',
            1 => 'Individu',
            2 => 'Sekolah',
            3 => 'Usaha',
            4 => 'Masjid',
        ];
    }
}
