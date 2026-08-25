<?php

namespace App\Controllers;

use App\Models\IndividuModel;
use App\Models\LembagaModel;
use App\Models\MasterIndividuModel;
use App\Models\MasterLembagaModel;
use App\Models\PekerjaanModel;
use App\Models\PenghasilanModel;
use App\Models\ProvinsiModel;
use CodeIgniter\HTTP\Files\UploadedFile;

class MustahikController extends BaseController
{
    protected MasterIndividuModel $individuModel;
    protected MasterLembagaModel $lembagaModel;

    public function __construct()
    {
        $this->individuModel = new MasterIndividuModel();
        $this->lembagaModel  = new MasterLembagaModel();
    }

    // ------------------------------------------------------------------
    // Mustahik Individu (ms_individu)
    // ------------------------------------------------------------------

    public function individu()
    {
        $data = [
            'title'       => 'Mustahik Individu',
            'activeMenu'  => 'mustahik-individu',
            'mustahik'    => $this->individuModel->withWilayah()->orderBy('nama_mustahik', 'ASC')->findAll(),
            'provinsi'    => (new ProvinsiModel())->orderBy('nama_provinsi', 'ASC')->findAll(),
            'pekerjaan'   => (new PekerjaanModel())->orderBy('nama_pekerjaan', 'ASC')->findAll(),
            'penghasilan' => (new PenghasilanModel())->orderBy('id_penghasilan', 'ASC')->findAll(),
        ];

        return view('mustahik/individu', $data);
    }

    /**
     * NIK is only present as a POST field on create — the edit form shows it
     * in a disabled input (it's the route param there), and disabled inputs
     * are never submitted, so it must not be required on update.
     */
    private function individuRules(?string $nik = null): array
    {
        $rules = [
            'nama_mustahik'    => ['label' => 'Nama', 'rules' => 'required', 'errors' => ['required' => '{field} tidak boleh kosong']],
            'kelamin_mustahik' => ['label' => 'Jenis kelamin', 'rules' => 'required', 'errors' => ['required' => '{field} wajib dipilih']],
            'dusun'            => ['label' => 'Dusun/Nama Jalan', 'rules' => 'required', 'errors' => ['required' => '{field} tidak boleh kosong']],
        ];

        if ($nik === null) {
            $rules = ['nik' => ['label' => 'NIK', 'rules' => 'required|is_unique[ms_individu.nik]', 'errors' => ['required' => '{field} tidak boleh kosong', 'is_unique' => 'NIK sudah terdaftar']]] + $rules;
        }

        return $rules;
    }

    private function individuPostData(): array
    {
        return [
            'nama_mustahik'     => $this->request->getPost('nama_mustahik'),
            'kelamin_mustahik'  => $this->request->getPost('kelamin_mustahik'),
            'tempat_lahir'      => $this->request->getPost('tempat_lahir'),
            'tgl_lahir'         => $this->request->getPost('tgl_lahir') ?: null,
            'agama_mustahik'    => $this->request->getPost('agama_mustahik'),
            'alamat'            => susun_alamat_rt_rw(
                $this->request->getPost('dusun'),
                $this->request->getPost('rt'),
                $this->request->getPost('rw')
            ),
            'dusun'             => $this->request->getPost('dusun') ?: null,
            'rt'                => $this->request->getPost('rt') ?: null,
            'rw'                => $this->request->getPost('rw') ?: null,
            'provinsi'          => $this->request->getPost('provinsi') ?: null,
            'kabupaten'         => $this->request->getPost('kabupaten') ?: null,
            'kecamatan'         => $this->request->getPost('kecamatan') ?: null,
            'desa'              => $this->request->getPost('desa') ?: null,
            'status_pendidikan' => $this->request->getPost('status_pendidikan'),
            'status_marital'    => $this->request->getPost('status_marital'),
            'pekerjaan'         => $this->request->getPost('pekerjaan') ?: null,
            'penghasilan'       => $this->request->getPost('penghasilan') ?: null,
            'jml_keluarga'      => $this->request->getPost('jml_keluarga') ?: null,
            'no_handphone'      => $this->request->getPost('no_handphone'),
            'email'             => $this->request->getPost('email'),
            'kk'                => $this->request->getPost('kk'),
        ];
    }

    public function storeIndividu()
    {
        if (!$this->validate($this->individuRules())) {
            session()->setFlashdata('gagal', implode(' ', $this->validator->getErrors()));
        } else {
            $data        = $this->individuPostData();
            $data['nik'] = $this->request->getPost('nik');

            $fotoKtp = $this->moveUpload('foto_ktp', 'ktp');
            $fotoKk  = $this->moveUpload('foto_kk', 'kk');
            if ($fotoKtp) {
                $data['foto_ktp'] = $fotoKtp;
            }
            if ($fotoKk) {
                $data['foto_kk'] = $fotoKk;
            }

            $ok = $this->individuModel->insert($data) !== false;
            session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Data mustahik berhasil tersimpan!' : 'GAGAL menyimpan data!');
        }

        return redirect()->to(base_url('mustahik/individu'));
    }

    public function updateIndividu(string $nik)
    {
        if (!$this->validate($this->individuRules($nik))) {
            session()->setFlashdata('gagal', implode(' ', $this->validator->getErrors()));
        } else {
            $data = $this->individuPostData();

            // Keep an existing photo on file when the admin doesn't re-upload one.
            $fotoKtp = $this->moveUpload('foto_ktp', 'ktp');
            $fotoKk  = $this->moveUpload('foto_kk', 'kk');
            if ($fotoKtp) {
                $data['foto_ktp'] = $fotoKtp;
            }
            if ($fotoKk) {
                $data['foto_kk'] = $fotoKk;
            }

            $ok = $this->individuModel->update($nik, $data);
            session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Data mustahik berhasil diperbarui!' : 'GAGAL menyimpan data!');
        }

        return redirect()->to(base_url('mustahik/individu'));
    }

    public function deleteIndividu(string $nik)
    {
        $dipakai = (new IndividuModel())->where('nik', $nik)->countAllResults();

        if ($dipakai > 0) {
            session()->setFlashdata('gagal', 'Data mustahik tidak bisa dihapus karena masih terkait dengan pengajuan.');
        } else {
            $ok = $this->individuModel->delete($nik);
            session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Data mustahik berhasil dihapus!' : 'GAGAL menghapus data!');
        }

        return redirect()->to(base_url('mustahik/individu'));
    }

    /** Streams a mustahik's foto_ktp/foto_kk inline so admins can view it in a new tab. */
    public function dokumenIndividu(string $nik, string $jenis)
    {
        if (!in_array($jenis, ['ktp', 'kk'], true)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $mustahik = $this->individuModel->find($nik);
        $filename = $mustahik[$jenis === 'ktp' ? 'foto_ktp' : 'foto_kk'] ?? null;

        if (!$filename) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $path = WRITEPATH . 'uploads/ajuan/' . $jenis . '/' . $filename;

        if (!is_file($path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->response
            ->setHeader('Content-Type', mime_content_type($path))
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->setBody(file_get_contents($path));
    }

    // ------------------------------------------------------------------
    // Mustahik Lembaga (ms_lembaga)
    // ------------------------------------------------------------------

    public function lembaga()
    {
        $data = [
            'title'      => 'Mustahik Lembaga',
            'activeMenu' => 'mustahik-lembaga',
            'lembaga'    => $this->lembagaModel->withWilayah()->orderBy('nama_lembaga', 'ASC')->findAll(),
            'provinsi'   => (new ProvinsiModel())->orderBy('nama_provinsi', 'ASC')->findAll(),
        ];

        return view('mustahik/lembaga', $data);
    }

    private function lembagaRules(): array
    {
        return [
            'nomor_legalitas' => ['label' => 'Nomor legalitas', 'rules' => 'required', 'errors' => ['required' => '{field} tidak boleh kosong']],
            'nama_lembaga'    => ['label' => 'Nama lembaga', 'rules' => 'required', 'errors' => ['required' => '{field} tidak boleh kosong']],
            'bidang'          => ['label' => 'Bidang', 'rules' => 'required', 'errors' => ['required' => '{field} tidak boleh kosong']],
            'dusun'           => ['label' => 'Dusun/Nama Jalan', 'rules' => 'required', 'errors' => ['required' => '{field} tidak boleh kosong']],
            'nomor_telepon'   => ['label' => 'Nomor telepon', 'rules' => 'required', 'errors' => ['required' => '{field} tidak boleh kosong']],
            'email'           => ['label' => 'Email', 'rules' => 'required|valid_email', 'errors' => ['required' => '{field} tidak boleh kosong', 'valid_email' => '{field} tidak valid']],
            'nama_pj'         => ['label' => 'Nama penanggung jawab', 'rules' => 'required', 'errors' => ['required' => '{field} tidak boleh kosong']],
            'jabatan_pj'      => ['label' => 'Jabatan penanggung jawab', 'rules' => 'required', 'errors' => ['required' => '{field} tidak boleh kosong']],
        ];
    }

    private function lembagaPostData(): array
    {
        return [
            'nomor_legalitas'  => strtoupper(trim((string) $this->request->getPost('nomor_legalitas'))),
            'nama_lembaga'     => $this->request->getPost('nama_lembaga'),
            'bidang'           => $this->request->getPost('bidang'),
            'tahun_berdiri'    => $this->request->getPost('tahun_berdiri') ?: null,
            'npwp'             => $this->request->getPost('npwp'),
            'alamat'           => susun_alamat_rt_rw(
                $this->request->getPost('dusun'),
                $this->request->getPost('rt'),
                $this->request->getPost('rw')
            ),
            'dusun'            => $this->request->getPost('dusun') ?: null,
            'rt'               => $this->request->getPost('rt') ?: null,
            'rw'               => $this->request->getPost('rw') ?: null,
            'provinsi'         => $this->request->getPost('provinsi') ?: null,
            'kabupaten'        => $this->request->getPost('kabupaten') ?: null,
            'kecamatan'        => $this->request->getPost('kecamatan') ?: null,
            'desa'             => $this->request->getPost('desa') ?: null,
            'nomor_telepon'    => $this->request->getPost('nomor_telepon'),
            'email'            => $this->request->getPost('email'),
            'website'          => $this->request->getPost('website'),
            'nama_pj'          => $this->request->getPost('nama_pj'),
            'jabatan_pj'       => $this->request->getPost('jabatan_pj'),
            'sumber_pendanaan' => $this->request->getPost('sumber_pendanaan'),
            'nomor_rekening'   => $this->request->getPost('nomor_rekening'),
        ];
    }

    public function storeLembaga()
    {
        $valid = $this->lembagaRules();
        $valid['nomor_legalitas']['rules'] .= '|is_unique[ms_lembaga.nomor_legalitas]';

        if (!$this->validate($valid)) {
            session()->setFlashdata('gagal', implode(' ', $this->validator->getErrors()));
        } else {
            $ok = $this->lembagaModel->insert($this->lembagaPostData()) !== false;
            session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Data lembaga berhasil tersimpan!' : 'GAGAL menyimpan data!');
        }

        return redirect()->to(base_url('mustahik/lembaga'));
    }

    public function updateLembaga(int $id)
    {
        $existing = $this->lembagaModel->find($id);
        $valid    = $this->lembagaRules();

        if ($existing && strtoupper(trim((string) $this->request->getPost('nomor_legalitas'))) !== $existing['nomor_legalitas']) {
            $valid['nomor_legalitas']['rules'] .= '|is_unique[ms_lembaga.nomor_legalitas]';
        }

        if (!$this->validate($valid)) {
            session()->setFlashdata('gagal', implode(' ', $this->validator->getErrors()));
        } else {
            $ok = $this->lembagaModel->update($id, $this->lembagaPostData());
            session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Data lembaga berhasil diperbarui!' : 'GAGAL menyimpan data!');
        }

        return redirect()->to(base_url('mustahik/lembaga'));
    }

    public function deleteLembaga(int $id)
    {
        $lembaga = $this->lembagaModel->find($id);
        $dipakai = $lembaga ? (new LembagaModel())->where('nomor_lembaga', $lembaga['nomor_legalitas'])->countAllResults() : 0;

        if ($dipakai > 0) {
            session()->setFlashdata('gagal', 'Data lembaga tidak bisa dihapus karena masih terkait dengan pengajuan.');
        } else {
            $ok = $this->lembagaModel->delete($id);
            session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Data lembaga berhasil dihapus!' : 'GAGAL menghapus data!');
        }

        return redirect()->to(base_url('mustahik/lembaga'));
    }

    private function moveUpload(string $field, string $subdir): ?string
    {
        /** @var UploadedFile|null $file */
        $file = $this->request->getFile($field);

        if ($file === null || !$file->isValid() || $file->hasMoved()) {
            return null;
        }

        $newName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/ajuan/' . $subdir, $newName);

        return $newName;
    }
}
