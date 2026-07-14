<?php

namespace App\Controllers;

use App\Models\PemohonModel;
use App\Models\ProvinsiModel;

class PemohonController extends BaseController
{
    protected PemohonModel $pemohonModel;

    public function __construct()
    {
        $this->pemohonModel = new PemohonModel();
    }

    public function index()
    {
        $data = [
            'title'      => 'Data Pemohon',
            'activeMenu' => 'pemohon',
            'pemohon'    => $this->pemohonModel->withWilayah()->orderBy('nama_pemohon', 'ASC')->findAll(),
            'provinsi'   => (new ProvinsiModel())->orderBy('nama_provinsi', 'ASC')->findAll(),
        ];

        return view('pemohon/index', $data);
    }

    /**
     * NIK is only present as a POST field on create — the edit form shows it
     * in a disabled input (it's the route param there), and disabled inputs
     * are never submitted, so it must not be required on update.
     */
    private function rules(?string $nik = null): array
    {
        $rules = [
            'nama_pemohon'  => ['label' => 'Nama', 'rules' => 'required', 'errors' => ['required' => '{field} tidak boleh kosong']],
            'jenis_kelamin' => ['label' => 'Jenis kelamin', 'rules' => 'required', 'errors' => ['required' => '{field} wajib dipilih']],
            'tempat_lahir'  => ['label' => 'Tempat lahir', 'rules' => 'required', 'errors' => ['required' => '{field} tidak boleh kosong']],
            'tanggal_lahir' => ['label' => 'Tanggal lahir', 'rules' => 'required', 'errors' => ['required' => '{field} tidak boleh kosong']],
            'id_provinsi'   => ['label' => 'Provinsi', 'rules' => 'required', 'errors' => ['required' => '{field} wajib dipilih']],
            'id_kabupaten'  => ['label' => 'Kabupaten', 'rules' => 'required', 'errors' => ['required' => '{field} wajib dipilih']],
            'id_kecamatan'  => ['label' => 'Kecamatan', 'rules' => 'required', 'errors' => ['required' => '{field} wajib dipilih']],
            'id_kelurahan'  => ['label' => 'Kelurahan', 'rules' => 'required', 'errors' => ['required' => '{field} wajib dipilih']],
            'alamat_detail' => ['label' => 'Alamat', 'rules' => 'required', 'errors' => ['required' => '{field} tidak boleh kosong']],
            'agama'         => ['label' => 'Agama', 'rules' => 'required', 'errors' => ['required' => '{field} wajib dipilih']],
            'telepon'       => ['label' => 'Telepon', 'rules' => 'required', 'errors' => ['required' => '{field} tidak boleh kosong']],
            'email'         => ['label' => 'Email', 'rules' => 'required|valid_email', 'errors' => ['required' => '{field} tidak boleh kosong', 'valid_email' => '{field} tidak valid']],
        ];

        if ($nik === null) {
            $rules = ['nik' => ['label' => 'NIK', 'rules' => 'required|is_unique[tr_pemohon.nik]', 'errors' => ['required' => '{field} tidak boleh kosong', 'is_unique' => 'NIK sudah terdaftar']]] + $rules;
        }

        return $rules;
    }

    private function postData(): array
    {
        return [
            'nama_pemohon'  => $this->request->getPost('nama_pemohon'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'tempat_lahir'  => $this->request->getPost('tempat_lahir'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
            'id_provinsi'   => $this->request->getPost('id_provinsi'),
            'id_kabupaten'  => $this->request->getPost('id_kabupaten'),
            'id_kecamatan'  => $this->request->getPost('id_kecamatan'),
            'id_kelurahan'  => $this->request->getPost('id_kelurahan'),
            'alamat_detail' => $this->request->getPost('alamat_detail'),
            'agama'         => $this->request->getPost('agama'),
            'telepon'       => $this->request->getPost('telepon'),
            'email'         => $this->request->getPost('email'),
        ];
    }

    public function store()
    {
        if (!$this->validate($this->rules())) {
            session()->setFlashdata('gagal', implode(' ', $this->validator->getErrors()));
        } else {
            $data        = $this->postData();
            $data['nik'] = $this->request->getPost('nik');
            $ok          = $this->pemohonModel->insert($data) !== false;
            session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Data pemohon berhasil tersimpan!' : 'GAGAL menyimpan data!');
        }

        return redirect()->to(base_url('pemohon'));
    }

    public function update(string $nik)
    {
        if (!$this->validate($this->rules($nik))) {
            session()->setFlashdata('gagal', implode(' ', $this->validator->getErrors()));
        } else {
            $ok = $this->pemohonModel->update($nik, $this->postData());
            session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Data pemohon berhasil diperbarui!' : 'GAGAL menyimpan data!');
        }

        return redirect()->to(base_url('pemohon'));
    }

    public function delete(string $nik)
    {
        $ok = $this->pemohonModel->delete($nik);
        session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Data pemohon berhasil dihapus!' : 'GAGAL menghapus data! Pemohon mungkin masih memiliki pengajuan.');

        return redirect()->to(base_url('pemohon'));
    }
}
