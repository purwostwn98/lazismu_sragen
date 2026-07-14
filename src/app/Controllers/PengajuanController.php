<?php

namespace App\Controllers;

use App\Models\AjuanModel;
use App\Models\IndividuModel;
use App\Models\KategoriProgramModel;
use App\Models\LembagaModel;
use App\Models\LogAjuanModel;
use App\Models\MasterIndividuModel;
use App\Models\MasterLembagaModel;
use App\Models\PekerjaanModel;
use App\Models\PemohonModel;
use App\Models\PenghasilanModel;
use App\Models\PilarModel;
use App\Models\ProgramModel;
use App\Models\ProvinsiModel;
use App\Models\SyaratModel;
use TCPDF;

/**
 * Public, unauthenticated aid-program submission flow, modeled on
 * lazismu_reborn's Pemohon controller: check/register applicant by NIK,
 * fill in the program application (with Individu/Lembaga branching and
 * file uploads), get a tracking number, and check status later.
 *
 * Unlike reborn's AJAX-driven multi-step wizard, each step here is a
 * plain page/redirect — simpler and consistent with the rest of this app.
 */
class PengajuanController extends BaseController
{
    protected PemohonModel $pemohonModel;
    protected AjuanModel $ajuanModel;
    protected IndividuModel $individuModel;
    protected LembagaModel $lembagaModel;
    protected LogAjuanModel $logAjuanModel;
    protected MasterIndividuModel $masterIndividuModel;
    protected MasterLembagaModel $masterLembagaModel;

    public function __construct()
    {
        $this->pemohonModel        = new PemohonModel();
        $this->ajuanModel          = new AjuanModel();
        $this->individuModel       = new IndividuModel();
        $this->lembagaModel        = new LembagaModel();
        $this->logAjuanModel       = new LogAjuanModel();
        $this->masterIndividuModel = new MasterIndividuModel();
        $this->masterLembagaModel  = new MasterLembagaModel();
    }

    /**
     * AJAX: check whether this NIK already has a master mustahik profile,
     * so the applicant doesn't have to retype everything they've entered
     * before (mirrors reborn's ms_lembaga check-before-fill, extended to
     * individuals too).
     */
    public function cekIndividu()
    {
        $nik = trim((string) $this->request->getPost('nik_mustahik'));

        $data = $nik !== '' ? $this->masterIndividuModel->find($nik) : null;

        return $this->response->setJSON([
            'found' => (bool) $data,
            'data'  => $data,
        ]);
    }

    /** AJAX: same idea as cekIndividu(), keyed by nomor legalitas lembaga. */
    public function cekLembaga()
    {
        $nomor = strtoupper(trim((string) $this->request->getPost('nomor_lembaga')));

        $data = $nomor !== '' ? $this->masterLembagaModel->where('nomor_legalitas', $nomor)->first() : null;

        return $this->response->setJSON([
            'found' => (bool) $data,
            'data'  => $data,
        ]);
    }

    /** Step 1: enter NIK. */
    public function index()
    {
        return view('pengajuan/cek_nik', ['title' => 'Ajukan Bantuan']);
    }

    public function cekNik()
    {
        $nik = trim((string) $this->request->getPost('nik'));

        if ($nik === '' || !preg_match('/^\d{16}$/', $nik)) {
            session()->setFlashdata('gagal', 'NIK harus berupa 16 digit angka.');

            return redirect()->to(base_url('pengajuan'))->withInput();
        }

        if ($this->pemohonModel->find($nik)) {
            return redirect()->to(base_url('pengajuan/formulir?nik=' . $nik));
        }

        return redirect()->to(base_url('pengajuan/biodata?nik=' . $nik));
    }

    /** Step 2 (new applicants only): personal biodata. */
    public function biodata()
    {
        $nik = $this->request->getGet('nik');

        if (!$nik || !preg_match('/^\d{16}$/', $nik)) {
            return redirect()->to(base_url('pengajuan'));
        }

        if ($this->pemohonModel->find($nik)) {
            return redirect()->to(base_url('pengajuan/formulir?nik=' . $nik));
        }

        return view('pengajuan/biodata', [
            'title'    => 'Biodata Pemohon',
            'nik'      => $nik,
            'provinsi' => (new ProvinsiModel())->orderBy('nama_provinsi', 'ASC')->findAll(),
        ]);
    }

    public function storeBiodata()
    {
        $nik = $this->request->getPost('nik');

        $valid = [
            'nik'           => ['label' => 'NIK', 'rules' => 'required|exact_length[16]|is_unique[tr_pemohon.nik]', 'errors' => ['required' => '{field} tidak boleh kosong', 'exact_length' => '{field} harus 16 digit', 'is_unique' => '{field} sudah terdaftar']],
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

        if (!$this->validate($valid)) {
            session()->setFlashdata('gagal', implode(' ', $this->validator->getErrors()));

            return redirect()->to(base_url('pengajuan/biodata?nik=' . $nik))->withInput();
        }

        $ok = $this->pemohonModel->insert([
            'nik'           => $nik,
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
        ]) !== false;

        if (!$ok) {
            session()->setFlashdata('gagal', 'GAGAL menyimpan biodata!');

            return redirect()->to(base_url('pengajuan/biodata?nik=' . $nik))->withInput();
        }

        session()->setFlashdata('berhasil', 'Biodata berhasil disimpan. Silakan lanjutkan mengisi formulir ajuan.');

        return redirect()->to(base_url('pengajuan/formulir?nik=' . $nik));
    }

    /** Step 3: choose a program and fill in the application. */
    public function formulir()
    {
        $nik = $this->request->getGet('nik');
        $pemohon = $nik ? $this->pemohonModel->find($nik) : null;

        if (!$pemohon) {
            return redirect()->to(base_url('pengajuan'));
        }

        return view('pengajuan/formulir', [
            'title'      => 'Formulir Ajuan',
            'pemohon'    => $pemohon,
            'pilar'      => (new PilarModel())->orderBy('nama_pilar', 'ASC')->findAll(),
            'kategori'   => (new KategoriProgramModel())->where('status_kategori', 1)->orderBy('nama_kategori', 'ASC')->findAll(),
            'program'    => (new ProgramModel())->where('status_program', 1)->orderBy('nama_program', 'ASC')->findAll(),
            'syarat'     => (new SyaratModel())->findAll(),
            'provinsi'   => (new ProvinsiModel())->orderBy('nama_provinsi', 'ASC')->findAll(),
            'pekerjaan'  => (new PekerjaanModel())->orderBy('nama_pekerjaan', 'ASC')->findAll(),
            'penghasilan' => (new PenghasilanModel())->orderBy('id_penghasilan', 'ASC')->findAll(),
        ]);
    }

    public function storeFormulir()
    {
        $nik     = $this->request->getPost('nik');
        $pemohon = $nik ? $this->pemohonModel->find($nik) : null;

        if (!$pemohon) {
            session()->setFlashdata('gagal', 'Silakan cek NIK Anda terlebih dahulu.');

            return redirect()->to(base_url('pengajuan'));
        }

        $valid = [
            'id_kategori_program' => ['label' => 'Kategori', 'rules' => 'required', 'errors' => ['required' => '{field} wajib dipilih']],
            'id_program'          => ['label' => 'Kegiatan', 'rules' => 'required', 'errors' => ['required' => '{field} wajib dipilih']],
            'nilai_diajukan'      => ['label' => 'Nilai diajukan', 'rules' => 'required|numeric', 'errors' => ['required' => '{field} wajib diisi', 'numeric' => '{field} harus angka']],
            'deskripsi_ajuan'     => ['label' => 'Deskripsi', 'rules' => 'required', 'errors' => ['required' => '{field} wajib diisi']],
            'jenis_ajuan'         => ['label' => 'Jenis ajuan', 'rules' => 'required|in_list[Individu,Lembaga]', 'errors' => ['required' => '{field} wajib dipilih']],
            'file_proposal'       => ['label' => 'Proposal', 'rules' => 'uploaded[file_proposal]', 'errors' => ['uploaded' => '{field} wajib diunggah, dan harus memuat seluruh syarat program yang dipilih']],
        ];

        if (!$this->validate($valid)) {
            session()->setFlashdata('gagal', implode(' ', $this->validator->getErrors()));

            return redirect()->to(base_url('pengajuan/formulir?nik=' . $nik))->withInput();
        }

        $fileFormulir = $this->moveUpload('file_formulir', 'formulir');
        $fileProposal = $this->moveUpload('file_proposal', 'proposal');

        $nomorAjuan = $this->ajuanModel->simpan([
            'nik'                 => $nik,
            'id_kategori_program' => $this->request->getPost('id_kategori_program'),
            'id_program'          => $this->request->getPost('id_program'),
            'nilai_diajukan'      => $this->request->getPost('nilai_diajukan'),
            'deskripsi_ajuan'     => $this->request->getPost('deskripsi_ajuan'),
            'jenis_ajuan'         => $this->request->getPost('jenis_ajuan'),
            'file_formulir'       => $fileFormulir,
            'file_proposal'       => $fileProposal,
        ]);

        if (!$nomorAjuan) {
            session()->setFlashdata('gagal', 'GAGAL menyimpan ajuan!');

            return redirect()->to(base_url('pengajuan/formulir?nik=' . $nik))->withInput();
        }

        if ($this->request->getPost('jenis_ajuan') === 'Individu') {
            $nikMustahik = $this->request->getPost('nik_mustahik') ?: $nik;
            $fotoKtp     = $this->moveUpload('foto_ktp', 'ktp');
            $fotoKk      = $this->moveUpload('foto_kk', 'kk');

            $individuData = [
                'nik'               => $nikMustahik,
                'foto_ktp'          => $fotoKtp,
                'kk'                => $this->request->getPost('kk'),
                'foto_kk'           => $fotoKk,
                'nama_mustahik'     => $this->request->getPost('nama_mustahik'),
                'tempat_lahir'      => $this->request->getPost('tempat_lahir_mustahik'),
                'tgl_lahir'         => $this->request->getPost('tgl_lahir_mustahik') ?: null,
                'kelamin_mustahik'  => $this->request->getPost('kelamin_mustahik'),
                'agama_mustahik'    => $this->request->getPost('agama_mustahik'),
                'alamat'            => $this->request->getPost('alamat_mustahik'),
                'provinsi'          => $this->request->getPost('provinsi_mustahik'),
                'kabupaten'         => $this->request->getPost('kabupaten_mustahik'),
                'kecamatan'         => $this->request->getPost('kecamatan_mustahik'),
                'desa'              => $this->request->getPost('kelurahan_mustahik'),
                'status_pendidikan' => $this->request->getPost('status_pendidikan'),
                'status_marital'    => $this->request->getPost('status_marital'),
                'pekerjaan'         => $this->request->getPost('pekerjaan'),
                'penghasilan'       => $this->request->getPost('penghasilan'),
                'jml_keluarga'      => $this->request->getPost('jml_keluarga'),
                'no_handphone'      => $this->request->getPost('no_handphone'),
                'email'             => $this->request->getPost('email_mustahik'),
            ];

            // tr_individu only links to ms_individu (nik is a foreign key), so
            // the master profile must exist first. Keep a photo already on
            // file from overwriting when the applicant didn't re-upload one.
            $masterData = $individuData;
            if (!$fotoKtp) {
                unset($masterData['foto_ktp']);
            }
            if (!$fotoKk) {
                unset($masterData['foto_kk']);
            }
            $this->masterIndividuModel->upsert($masterData);

            $this->individuModel->insert(['nomor_ajuan' => $nomorAjuan, 'nik' => $nikMustahik]);
        } else {
            $nomorLembaga = strtoupper(trim((string) $this->request->getPost('nomor_lembaga')));

            // Same idea: tr_lembaga links to ms_lembaga (nomor_lembaga is a
            // foreign key to nomor_legalitas), so upsert the master first.
            $this->masterLembagaModel->upsert([
                'nama_lembaga'     => $this->request->getPost('nama_lembaga'),
                'bidang'           => $this->request->getPost('bidang_lembaga') ?: '-',
                'tahun_berdiri'    => $this->request->getPost('tahun_berdiri_lembaga') ?: null,
                'nomor_legalitas'  => $nomorLembaga,
                'npwp'             => $this->request->getPost('npwp_lembaga') ?: null,
                'alamat'           => $this->request->getPost('alamat_lembaga'),
                'nomor_telepon'    => $this->request->getPost('telepon_lembaga') ?: '-',
                'email'            => $this->request->getPost('email_lembaga') ?: 'belum@diisi.com',
                'website'          => $this->request->getPost('website_lembaga') ?: null,
                'nama_pj'          => $this->request->getPost('nama_pj_lembaga') ?: '-',
                'jabatan_pj'       => $this->request->getPost('jabatan_pj_lembaga') ?: '-',
                'sumber_pendanaan' => $this->request->getPost('sumber_pendanaan_lembaga') ?: null,
                'nomor_rekening'   => $this->request->getPost('nomor_rekening_lembaga') ?: null,
            ]);

            $this->lembagaModel->insert(['nomor_ajuan' => $nomorAjuan, 'nomor_lembaga' => $nomorLembaga]);
        }

        $this->logAjuanModel->catat($nomorAjuan, 1, 'Ajuan diajukan melalui formulir online');

        return redirect()->to(base_url('pengajuan/sukses/' . $nomorAjuan));
    }

    /** Step 4: confirmation with the tracking number. */
    public function sukses(string $nomorAjuan)
    {
        $ajuan = $this->ajuanModel->withRelasi()->where('tr_ajuan.nomor_ajuan', $nomorAjuan)->first();

        if (!$ajuan) {
            return redirect()->to(base_url('pengajuan'));
        }

        return view('pengajuan/sukses', ['title' => 'Ajuan Terkirim', 'ajuan' => $ajuan]);
    }

    /** Printable PDF receipt for a submitted ajuan, shown from the success page. */
    public function pdfBukti(string $nomorAjuan)
    {
        $ajuan = $this->ajuanModel->withRelasi()->where('tr_ajuan.nomor_ajuan', $nomorAjuan)->first();

        if (!$ajuan) {
            return redirect()->to(base_url('pengajuan'));
        }

        $pemohon = $this->pemohonModel->withWilayah()->where('tr_pemohon.nik', $ajuan['nik'])->first();
        $individu = $ajuan['jenis_ajuan'] === 'Individu'
            ? $this->individuModel->withMustahikLengkap()->where('tr_individu.nomor_ajuan', $nomorAjuan)->first()
            : null;
        $lembaga = $ajuan['jenis_ajuan'] === 'Lembaga'
            ? $this->lembagaModel->withLembaga()->where('tr_lembaga.nomor_ajuan', $nomorAjuan)->first()
            : null;

        $html = view('pengajuan/pdf/bukti_ajuan', [
            'ajuan'    => $ajuan,
            'pemohon'  => $pemohon,
            'individu' => $individu,
            'lembaga'  => $lembaga,
        ]);

        $pdf = new TCPDF('P', PDF_UNIT, 'A5', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Lazismu Sragen');
        $pdf->SetTitle('Bukti Ajuan ' . $nomorAjuan);
        $pdf->SetSubject('Bukti Ajuan ' . $nomorAjuan);
        $pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');

        // QR code that links to a public verification page, so anyone holding
        // the printed document can confirm it was genuinely issued by this
        // system (and wasn't altered) without exposing any personal data
        // beyond what's already printed on the page.
        $pdf->Ln(2);
        $pdf->write2DBarcode(
            base_url('pengajuan/verifikasi/' . $nomorAjuan),
            'QRCODE,H',
            '',
            '',
            26,
            26,
            ['border' => false, 'padding' => 0, 'fgcolor' => [0, 0, 0], 'bgcolor' => false, 'position' => 'C'],
            'N'
        );
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(0, 4, 'Verifikasi keaslian dokumen: ' . $nomorAjuan, 0, 1, 'C');

        return $this->response
            ->setContentType('application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="Bukti-Ajuan-' . $nomorAjuan . '.pdf"')
            ->setBody($pdf->Output('Bukti-Ajuan-' . $nomorAjuan . '.pdf', 'S'));
    }

    /**
     * Public, read-only verification page a QR code on the printed receipt
     * points to. Only shows the same non-sensitive fields already printed on
     * the document (no NIK, address, or mustahik details) to confirm the
     * receipt matches a real record without exposing extra personal data.
     */
    public function verifikasi(string $nomorAjuan)
    {
        $ajuan = $this->ajuanModel->withRelasi()->where('tr_ajuan.nomor_ajuan', $nomorAjuan)->first();

        return view('pengajuan/verifikasi', ['title' => 'Verifikasi Dokumen', 'ajuan' => $ajuan]);
    }

    /** Public status check by NIK + nomor ajuan (no login required). */
    public function statusForm()
    {
        return view('pengajuan/cek_status', ['title' => 'Cek Status Ajuan']);
    }

    public function cekStatus()
    {
        $nik        = trim((string) $this->request->getPost('nik'));
        $nomorAjuan = trim((string) $this->request->getPost('nomor_ajuan'));

        $ajuan = $this->ajuanModel->withRelasi()
            ->where('tr_ajuan.nik', $nik)
            ->where('tr_ajuan.nomor_ajuan', $nomorAjuan)
            ->first();

        if (!$ajuan) {
            session()->setFlashdata('gagal', 'Data ajuan tidak ditemukan. Periksa kembali NIK dan nomor ajuan Anda.');

            return redirect()->to(base_url('pengajuan/status'))->withInput();
        }

        return view('pengajuan/status_hasil', [
            'title' => 'Status Ajuan',
            'ajuan' => $ajuan,
            'logs'  => $this->logAjuanModel->where('nomor_ajuan', $nomorAjuan)->orderBy('tanggal_log', 'DESC')->findAll(),
        ]);
    }

    private function moveUpload(string $field, string $subdir): ?string
    {
        $file = $this->request->getFile($field);

        if ($file === null || !$file->isValid() || $file->hasMoved()) {
            return null;
        }

        $newName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/ajuan/' . $subdir, $newName);

        return $newName;
    }
}
