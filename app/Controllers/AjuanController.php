<?php

namespace App\Controllers;

use App\Models\AjuanModel;
use App\Models\BentukPenyerahanModel;
use App\Models\BeritaAcaraModel;
use App\Models\DelegasiStModel;
use App\Models\DokumentasiModel;
use App\Models\FormB3Model;
use App\Models\IndividuModel;
use App\Models\JabatanPenjabatModel;
use App\Models\KategoriPenerimaModel;
use App\Models\KategoriProgramModel;
use App\Models\KuitansiModel;
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
use App\Models\StatusAjuanModel;
use App\Models\SuratTugasModel;
use App\Models\SyaratModel;
use CodeIgniter\HTTP\Files\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;
use TCPDF;

class AjuanController extends BaseController
{
    /** Nominal disetujui threshold above which the C1 export uses the "DIATAS 5JT" template (with a Ketua Badan Pengurus signature column). */
    private const AMBANG_C1_BESAR = 5000000;

    protected AjuanModel $ajuanModel;
    protected IndividuModel $individuModel;
    protected LembagaModel $lembagaModel;
    protected DokumentasiModel $dokumentasiModel;
    protected KuitansiModel $kuitansiModel;
    protected LogAjuanModel $logAjuanModel;
    protected StatusAjuanModel $statusAjuanModel;
    protected ProgramModel $programModel;
    protected PemohonModel $pemohonModel;
    protected MasterIndividuModel $masterIndividuModel;
    protected MasterLembagaModel $masterLembagaModel;
    protected FormB3Model $formB3Model;
    protected KategoriPenerimaModel $kategoriPenerimaModel;
    protected BentukPenyerahanModel $bentukPenyerahanModel;
    protected SuratTugasModel $suratTugasModel;
    protected DelegasiStModel $delegasiStModel;
    protected BeritaAcaraModel $beritaAcaraModel;

    public function __construct()
    {
        $this->ajuanModel            = new AjuanModel();
        $this->individuModel         = new IndividuModel();
        $this->lembagaModel          = new LembagaModel();
        $this->dokumentasiModel      = new DokumentasiModel();
        $this->kuitansiModel         = new KuitansiModel();
        $this->logAjuanModel         = new LogAjuanModel();
        $this->statusAjuanModel      = new StatusAjuanModel();
        $this->programModel          = new ProgramModel();
        $this->pemohonModel          = new PemohonModel();
        $this->masterIndividuModel   = new MasterIndividuModel();
        $this->masterLembagaModel    = new MasterLembagaModel();
        $this->formB3Model           = new FormB3Model();
        $this->kategoriPenerimaModel = new KategoriPenerimaModel();
        $this->bentukPenyerahanModel = new BentukPenyerahanModel();
        $this->suratTugasModel       = new SuratTugasModel();
        $this->beritaAcaraModel      = new BeritaAcaraModel();
        $this->delegasiStModel       = new DelegasiStModel();
    }

    /** AJAX: check for an existing master mustahik profile by NIK. */
    public function cekIndividu()
    {
        $nik  = trim((string) $this->request->getPost('nik_mustahik'));
        $data = $nik !== '' ? $this->masterIndividuModel->find($nik) : null;

        return $this->response->setJSON(['found' => (bool) $data, 'data' => $data]);
    }

    /** AJAX: check for an existing master lembaga profile by nomor legalitas. */
    public function cekLembaga()
    {
        $nomor = strtoupper(trim((string) $this->request->getPost('nomor_lembaga')));
        $data  = $nomor !== '' ? $this->masterLembagaModel->where('nomor_legalitas', $nomor)->first() : null;

        return $this->response->setJSON(['found' => (bool) $data, 'data' => $data]);
    }

    public function index()
    {
        return view('ajuan/index', array_merge(
            ['title' => 'Data Ajuan', 'activeMenu' => 'ajuan'],
            $this->groupedAjuan(null)
        ));
    }

    public function individu()
    {
        return view('ajuan/index', array_merge(
            ['title' => 'Ajuan Individu', 'activeMenu' => 'ajuan-individu'],
            $this->groupedAjuan('Individu')
        ));
    }

    public function lembaga()
    {
        return view('ajuan/index', array_merge(
            ['title' => 'Ajuan Lembaga', 'activeMenu' => 'ajuan-lembaga'],
            $this->groupedAjuan('Lembaga')
        ));
    }

    public function rutin()
    {
        $rows = $this->ajuanModel->withRelasi()
            ->whereIn('tr_ajuan.status_ajuan', [8])
            ->orderBy('tgl_diajukan', 'DESC')
            ->findAll();

        return view('ajuan/rutin', [
            'title'      => 'Ajuan Rutin',
            'activeMenu' => 'ajuan-rutin',
            'rows'       => $rows,
        ]);
    }

    /**
     * Splits ajuan into the same status groups lazismu_reborn's admin tabs
     * use (Baru/Proses/Rutin/Selesai/Ditolak), optionally scoped to one
     * jenis_ajuan. All tabs are rendered up front (no per-tab AJAX round
     * trip) since the data volume here is small.
     */
    private function groupedAjuan(?string $jenis): array
    {
        $statusGroups = [
            'ajuanBaru'    => [0, 1],
            'ajuanProses'  => [2, 3, 4, 5],
            'ajuanRutin'   => [8],
            'ajuanSelesai' => [7, 9],
            'ajuanDitolak' => [6],
        ];

        $result = [];
        foreach ($statusGroups as $key => $statuses) {
            $query = $this->ajuanModel->withRelasi()->whereIn('tr_ajuan.status_ajuan', $statuses);
            if ($jenis !== null) {
                $query->where('tr_ajuan.jenis_ajuan', $jenis);
            }
            $result[$key] = $query->orderBy('tgl_diajukan', 'DESC')->findAll();
        }

        return $result;
    }

    public function create()
    {
        $data = [
            'title'      => 'Tambah Ajuan',
            'activeMenu' => 'ajuan',
            'pemohon'    => $this->pemohonModel->orderBy('nama_pemohon', 'ASC')->findAll(),
            'pilar'      => (new PilarModel())->orderBy('nama_pilar', 'ASC')->findAll(),
            'kategori'   => (new KategoriProgramModel())->where('status_kategori', 1)->orderBy('nama_kategori', 'ASC')->findAll(),
            'program'    => $this->programModel->where('status_program', 1)->orderBy('nama_program', 'ASC')->findAll(),
            'syarat'     => (new SyaratModel())->findAll(),
            'provinsi'   => (new ProvinsiModel())->orderBy('nama_provinsi', 'ASC')->findAll(),
            'pekerjaan'  => (new PekerjaanModel())->orderBy('nama_pekerjaan', 'ASC')->findAll(),
            'penghasilan' => (new PenghasilanModel())->orderBy('id_penghasilan', 'ASC')->findAll(),
        ];

        return view('ajuan/create', $data);
    }

    public function store()
    {
        $valid = [
            'nik'                 => ['label' => 'Pemohon', 'rules' => 'required', 'errors' => ['required' => '{field} wajib dipilih']],
            'id_kategori_program'  => ['label' => 'Kategori', 'rules' => 'required', 'errors' => ['required' => '{field} wajib dipilih']],
            'id_program'          => ['label' => 'Kegiatan', 'rules' => 'required', 'errors' => ['required' => '{field} wajib dipilih']],
            'nilai_diajukan'      => ['label' => 'Nilai diajukan', 'rules' => 'required|numeric', 'errors' => ['required' => '{field} wajib diisi', 'numeric' => '{field} harus angka']],
            'deskripsi_ajuan'     => ['label' => 'Deskripsi', 'rules' => 'required', 'errors' => ['required' => '{field} wajib diisi']],
            'jenis_ajuan'         => ['label' => 'Jenis ajuan', 'rules' => 'required|in_list[Individu,Lembaga]', 'errors' => ['required' => '{field} wajib dipilih']],
            'file_proposal'       => ['label' => 'Proposal', 'rules' => 'uploaded[file_proposal]', 'errors' => ['uploaded' => '{field} wajib diunggah, dan harus memuat seluruh syarat program yang dipilih']],
        ];

        if (!$this->validate($valid)) {
            session()->setFlashdata('gagal', implode(' ', $this->validator->getErrors()));

            return redirect()->back()->withInput();
        }

        $fileFormulir = $this->moveUpload('file_formulir', 'formulir');
        $fileProposal = $this->moveUpload('file_proposal', 'proposal');

        $nomorAjuan = $this->ajuanModel->simpan([
            'nik'                 => $this->request->getPost('nik'),
            'id_kategori_program' => $this->request->getPost('id_kategori_program'),
            'id_program'          => $this->request->getPost('id_program'),
            'nilai_diajukan'      => $this->request->getPost('nilai_diajukan'),
            'deskripsi_ajuan'     => $this->request->getPost('deskripsi_ajuan'),
            'jenis_ajuan'         => $this->request->getPost('jenis_ajuan'),
            'file_formulir'       => $fileFormulir,
            'file_proposal'       => $fileProposal,
        ]);

        if (!$nomorAjuan) {
            session()->setFlashdata('gagal', 'GAGAL menyimpan data ajuan!');

            return redirect()->to(base_url('ajuan/create'))->withInput();
        }

        if ($this->request->getPost('jenis_ajuan') === 'Individu') {
            $nikMustahik = $this->request->getPost('nik_mustahik') ?: $this->request->getPost('nik');
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
                'alamat'            => susun_alamat_rt_rw(
                    $this->request->getPost('dusun_mustahik'),
                    $this->request->getPost('rt_mustahik'),
                    $this->request->getPost('rw_mustahik')
                ),
                'dusun'             => $this->request->getPost('dusun_mustahik') ?: null,
                'rt'                => $this->request->getPost('rt_mustahik') ?: null,
                'rw'                => $this->request->getPost('rw_mustahik') ?: null,
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
                'alamat'           => susun_alamat_rt_rw(
                    $this->request->getPost('dusun_lembaga'),
                    $this->request->getPost('rt_lembaga'),
                    $this->request->getPost('rw_lembaga')
                ),
                'dusun'            => $this->request->getPost('dusun_lembaga') ?: null,
                'rt'               => $this->request->getPost('rt_lembaga') ?: null,
                'rw'               => $this->request->getPost('rw_lembaga') ?: null,
                'provinsi'         => $this->request->getPost('provinsi_lembaga') ?: null,
                'kabupaten'        => $this->request->getPost('kabupaten_lembaga') ?: null,
                'kecamatan'        => $this->request->getPost('kecamatan_lembaga') ?: null,
                'desa'             => $this->request->getPost('kelurahan_lembaga') ?: null,
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

        $this->logAjuanModel->catat($nomorAjuan, 1, 'Ajuan diajukan');

        session()->setFlashdata('berhasil', "Ajuan berhasil diajukan dengan nomor {$nomorAjuan}!");

        return redirect()->to(base_url('ajuan/' . $nomorAjuan));
    }

    public function show(string $nomorAjuan)
    {
        $ajuan = $this->ajuanModel->withRelasi()->where('tr_ajuan.nomor_ajuan', $nomorAjuan)->first();

        if (!$ajuan) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $b3 = $this->formB3Model
            ->select('tr_form_b3.*, ktg.ket_kategori_penerima, bp.ket_bentuk_penyerahan, prg.nama_program, kp.nama_kategori, pil.nama_pilar')
            ->join('dt_kategori_penerima ktg', 'ktg.id_kategori_penerima = tr_form_b3.kategori_penerima')
            ->join('dt_bentuk_penyerahan bp', 'bp.id_bentuk_penyerahan = tr_form_b3.bentuk_penyerahan')
            ->join('ad_program prg', 'prg.id_program = tr_form_b3.id_program', 'left')
            ->join('ad_kategori_program kp', 'kp.id_kategori_program = tr_form_b3.id_kategori_program', 'left')
            ->join('dt_pilar pil', 'pil.id_pilar = kp.id_pilar', 'left')
            ->where('tr_form_b3.nomor_ajuan', $nomorAjuan)
            ->first();

        $kategoriProgramList = (new KategoriProgramModel())->where('status_kategori', 1)->orderBy('nama_kategori', 'ASC')->findAll();

        $b3Initial = null;
        if ($b3 && !empty($b3['id_kategori_program'])) {
            $kategoriMatch = null;
            foreach ($kategoriProgramList as $k) {
                if ((string) $k['id_kategori_program'] === (string) $b3['id_kategori_program']) {
                    $kategoriMatch = $k;
                    break;
                }
            }
            $b3Initial = [
                'idPilar'           => $kategoriMatch['id_pilar'] ?? null,
                'idKategoriProgram' => $b3['id_kategori_program'],
                'idProgram'         => $b3['id_program'],
            ];
        }

        $suratTugas = $this->suratTugasModel->where('nomor_ajuan', $nomorAjuan)->orderBy('id_surat_tugas', 'DESC')->findAll();

        $suratTugasIds = array_column($suratTugas, 'id_surat_tugas');
        $delegasiByST  = [];
        if ($suratTugasIds) {
            foreach ($this->delegasiStModel->whereIn('id_surat_tugas', $suratTugasIds)->findAll() as $d) {
                $delegasiByST[$d['id_surat_tugas']][] = $d['nama_delegasi'];
            }
        }
        foreach ($suratTugas as &$st) {
            $st['delegasi'] = $delegasiByST[$st['id_surat_tugas']] ?? [];
        }
        unset($st);

        $beritaAcara = $this->beritaAcaraModel
            ->select('ad_berita_acara.*, ktg.ket_kategori_penerima, bp.ket_bentuk_penyerahan')
            ->join('dt_kategori_penerima ktg', 'ktg.id_kategori_penerima = ad_berita_acara.kategori_penerima')
            ->join('dt_bentuk_penyerahan bp', 'bp.id_bentuk_penyerahan = ad_berita_acara.bentuk_penyerahan')
            ->where('ad_berita_acara.nomor_ajuan', $nomorAjuan)
            ->orderBy('ad_berita_acara.id_berita_acara', 'DESC')
            ->findAll();

        $activeMenu = $ajuan['jenis_ajuan'] === 'Individu' ? 'ajuan-individu' : 'ajuan-lembaga';
        if ($this->request->getGet('from') === 'rutin') {
            $activeMenu = 'ajuan-rutin';
        }

        $data = [
            'title'       => 'Detail Ajuan ' . $nomorAjuan,
            'activeMenu'  => $activeMenu,
            'ajuan'       => $ajuan,
            'individu'    => $ajuan['jenis_ajuan'] === 'Individu' ? $this->individuModel->withMustahik()->where('tr_individu.nomor_ajuan', $nomorAjuan)->first() : null,
            'lembaga'     => $ajuan['jenis_ajuan'] === 'Lembaga' ? $this->lembagaModel->withLembaga()->where('tr_lembaga.nomor_ajuan', $nomorAjuan)->first() : null,
            'logs'        => $this->logAjuanModel->where('nomor_ajuan', $nomorAjuan)->orderBy('tanggal_log', 'DESC')->findAll(),
            'dokumentasi' => $this->dokumentasiModel->where('no_ajuan', $nomorAjuan)->orderBy('created_at', 'DESC')->findAll(),
            'kuitansi'    => $this->kuitansiModel->where('no_ajuan', $nomorAjuan)->orderBy('created_at', 'DESC')->findAll(),
            'statusList'  => $this->statusAjuanModel->orderBy('id_status', 'ASC')->findAll(),
            'b3'          => $b3,
            'b3Initial'   => $b3Initial,
            'kategoriPenerima'    => $this->kategoriPenerimaModel->orderBy('id_dana_dari', 'ASC')->findAll(),
            'bentukPenyerahan'    => $this->bentukPenyerahanModel->orderBy('id_bentuk_penyerahan', 'ASC')->findAll(),
            'pilarList'           => (new PilarModel())->orderBy('nama_pilar', 'ASC')->findAll(),
            'kategoriProgramList' => $kategoriProgramList,
            'programList'         => $this->programModel->where('status_program', 1)->orderBy('nama_program', 'ASC')->findAll(),
            'suratTugas'          => $suratTugas,
            'beritaAcara'         => $beritaAcara,
        ];

        return view('ajuan/show', $data);
    }

    public function updateStatus(string $nomorAjuan)
    {
        $statusBaru   = (int) $this->request->getPost('status_ajuan');
        $sifatBantuan = $this->request->getPost('sifat_bantuan');
        $catatan      = $this->request->getPost('catatan_log');
        $tanggalLog   = $this->request->getPost('tanggal_log');

        $update = [
            'status_ajuan'  => $statusBaru,
            'sifat_bantuan' => $sifatBantuan,
        ];

        // Menunggu Rapat/Rapat Pengurus/Menunggu Survey/Survey let the admin
        // backdate the log to when that event actually happened.
        $logTanggal = null;
        if (in_array($statusBaru, [2, 3, 4, 5], true) && $tanggalLog) {
            $logTanggal = $tanggalLog . ' 00:00:00';
        }

        // Disetujui / Disetujui (Rutin) also record the approved amount.
        if (in_array($statusBaru, [7, 8], true)) {
            $update['nilai_disetujui']    = (float) $this->request->getPost('nilai_disetujui');
            $update['status_tersalurkan'] = 'belum';
        }

        $ok = $this->ajuanModel->where('nomor_ajuan', $nomorAjuan)->set($update)->update();

        if ($ok) {
            $this->logAjuanModel->catat($nomorAjuan, $statusBaru, $catatan, null, $logTanggal);
        }

        session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Status ajuan berhasil diperbarui!' : 'GAGAL memperbarui status!');

        return redirect()->to(base_url('ajuan/' . $nomorAjuan));
    }

    /** Upserts the B3 fund-source/disbursement classification for an ajuan (one row per nomor_ajuan). */
    public function simpanB3(string $nomorAjuan)
    {
        $valid = [
            'id_kategori_program' => ['label' => 'Kategori program', 'rules' => 'required|numeric', 'errors' => ['required' => '{field} wajib dipilih']],
            'id_program'          => ['label' => 'Kegiatan', 'rules' => 'required|numeric', 'errors' => ['required' => '{field} wajib dipilih']],
            'dana_dari'           => ['label' => 'Sumber dana', 'rules' => 'required', 'errors' => ['required' => '{field} wajib dipilih']],
            'kategori_penerima'   => ['label' => 'Asnaf/kategori penerima', 'rules' => 'required|numeric', 'errors' => ['required' => '{field} wajib dipilih']],
            'bentuk_penyerahan'   => ['label' => 'Bentuk penyerahan', 'rules' => 'required|numeric', 'errors' => ['required' => '{field} wajib dipilih']],
        ];

        if (!$this->validate($valid)) {
            session()->setFlashdata('gagal', implode(' ', $this->validator->getErrors()));

            return redirect()->to(base_url('ajuan/' . $nomorAjuan));
        }

        $this->formB3Model->upsert($nomorAjuan, [
            'id_kategori_program' => $this->request->getPost('id_kategori_program'),
            'id_program'          => $this->request->getPost('id_program'),
            'dana_dari'           => $this->request->getPost('dana_dari'),
            'kategori_penerima'   => $this->request->getPost('kategori_penerima'),
            'bentuk_penyerahan'   => $this->request->getPost('bentuk_penyerahan'),
        ]);

        session()->setFlashdata('berhasil', 'Data B3 berhasil disimpan!');

        return redirect()->to(base_url('ajuan/' . $nomorAjuan));
    }

    /** Creates a new Surat Tugas (an ajuan can have several over its lifecycle, so this always inserts). */
    public function simpanSuratTugas(string $nomorAjuan)
    {
        $valid = [
            'nama_penanggung_jawab' => ['label' => 'Nama penanggung jawab', 'rules' => 'required', 'errors' => ['required' => '{field} wajib diisi']],
            'jabatan'               => ['label' => 'Jabatan', 'rules' => 'required', 'errors' => ['required' => '{field} wajib diisi']],
            'berdasarkan'           => ['label' => 'Berdasarkan', 'rules' => 'required|in_list[Rapat Pengurus,Surat/Proposal]', 'errors' => ['required' => '{field} wajib dipilih']],
            'agenda'                => ['label' => 'Agenda', 'rules' => 'required|in_list[Survey,Menyalurkan,Monev,Koordinasi,Lainnya]', 'errors' => ['required' => '{field} wajib dipilih']],
            'tanggal_mulai'         => ['label' => 'Tanggal mulai', 'rules' => 'required|valid_date', 'errors' => ['required' => '{field} wajib diisi']],
            'tanggal_selesai'       => ['label' => 'Tanggal selesai', 'rules' => 'required|valid_date', 'errors' => ['required' => '{field} wajib diisi']],
            'lokasi'                => ['label' => 'Tempat/lokasi', 'rules' => 'required', 'errors' => ['required' => '{field} wajib diisi']],
        ];

        if (!$this->validate($valid)) {
            session()->setFlashdata('gagal', implode(' ', $this->validator->getErrors()));

            return redirect()->to(base_url('ajuan/' . $nomorAjuan));
        }

        $idSuratTugas = $this->suratTugasModel->insert([
            'nomor_ajuan'           => $nomorAjuan,
            'nama_penanggung_jawab' => $this->request->getPost('nama_penanggung_jawab'),
            'jabatan'               => $this->request->getPost('jabatan'),
            'berdasarkan'           => $this->request->getPost('berdasarkan'),
            'agenda'                => $this->request->getPost('agenda'),
            // <input type="datetime-local"> submits 'Y-m-d\TH:i'; MySQL's
            // datetime column needs the space-separated form.
            'tanggal_mulai'         => str_replace('T', ' ', (string) $this->request->getPost('tanggal_mulai')),
            'tanggal_selesai'       => str_replace('T', ' ', (string) $this->request->getPost('tanggal_selesai')),
            'lokasi'                => $this->request->getPost('lokasi'),
        ]);

        foreach ((array) $this->request->getPost('nama_delegasi') as $nama) {
            $nama = trim((string) $nama);
            if ($nama !== '') {
                $this->delegasiStModel->insert(['id_surat_tugas' => $idSuratTugas, 'nama_delegasi' => $nama]);
            }
        }

        session()->setFlashdata('berhasil', 'Surat tugas berhasil dibuat!');

        return redirect()->to(base_url('ajuan/' . $nomorAjuan));
    }

    /** Attaches a proof/result file (scan, survey photos, etc.) to an existing Surat Tugas. */
    public function uploadBuktiSuratTugas(int $idSuratTugas)
    {
        $suratTugas = $this->suratTugasModel->find($idSuratTugas);

        if (!$suratTugas) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $file = $this->moveUpload('file_surat_tugas', 'surat_tugas');

        if ($file) {
            $this->suratTugasModel->update($idSuratTugas, ['file_surat_tugas' => $file]);
            session()->setFlashdata('berhasil', 'Bukti surat tugas berhasil diunggah!');
        } else {
            session()->setFlashdata('gagal', 'File bukti wajib diunggah!');
        }

        return redirect()->to(base_url('ajuan/' . $suratTugas['nomor_ajuan']));
    }

    /** Streams a Surat Tugas's uploaded proof file inline. */
    public function fileSuratTugas(int $idSuratTugas)
    {
        $suratTugas = $this->suratTugasModel->find($idSuratTugas);

        if (!$suratTugas || !$suratTugas['file_surat_tugas']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $path = WRITEPATH . 'uploads/ajuan/surat_tugas/' . $suratTugas['file_surat_tugas'];

        if (!is_file($path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->response
            ->setHeader('Content-Type', mime_content_type($path))
            ->setHeader('Content-Disposition', 'inline; filename="' . $suratTugas['file_surat_tugas'] . '"')
            ->setBody(file_get_contents($path));
    }

    /** Printable PDF of a Surat Tugas letter. */
    public function pdfSuratTugas(int $idSuratTugas)
    {
        $suratTugas = $this->suratTugasModel->find($idSuratTugas);

        if (!$suratTugas) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $ajuan    = $this->ajuanModel->withRelasi()->where('tr_ajuan.nomor_ajuan', $suratTugas['nomor_ajuan'])->first();
        $delegasi = $this->delegasiStModel->where('id_surat_tugas', $idSuratTugas)->findAll();

        // status_ajuan = 3 ("Rapat Pengurus") is when the board-meeting date
        // was logged, referenced here when the letter's authority is that meeting.
        $rapatLog = $suratTugas['berdasarkan'] === 'Rapat Pengurus'
            ? $this->logAjuanModel->where('nomor_ajuan', $suratTugas['nomor_ajuan'])->where('status_ajuan', 3)->first()
            : null;

        $html = view('ajuan/pdf/surat_tugas', [
            'suratTugas'   => $suratTugas,
            'ajuan'        => $ajuan,
            'delegasi'     => $delegasi,
            'tanggalRapat' => $rapatLog['tanggal_log'] ?? null,
        ]);

        $pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Lazismu Sragen');
        $pdf->SetTitle('Surat Tugas ' . $idSuratTugas);
        $pdf->SetSubject('Surat Tugas ' . $idSuratTugas);
        $pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');

        return $this->response
            ->setContentType('application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="Surat-Tugas-' . $idSuratTugas . '.pdf"')
            ->setBody($pdf->Output('Surat-Tugas-' . $idSuratTugas . '.pdf', 'S'));
    }

    /**
     * Creates a new Berita Acara (an ajuan can have several, e.g. staged
     * disbursements, so this always inserts). If a Form B3 record already
     * exists for this ajuan, its dana_dari/kategori_penerima/bentuk_penyerahan
     * are authoritative and override whatever was submitted, so the two
     * records can't disagree on fund classification.
     */
    public function simpanBeritaAcara(string $nomorAjuan)
    {
        $b3 = $this->formB3Model->where('nomor_ajuan', $nomorAjuan)->first();

        $valid = [
            'yang_bertandatangan' => ['label' => 'Yang bertandatangan', 'rules' => 'required', 'errors' => ['required' => '{field} wajib diisi']],
            'tanggal_penyerahan'  => ['label' => 'Tanggal penyerahan', 'rules' => 'required|valid_date', 'errors' => ['required' => '{field} wajib diisi']],
            'lokasi_penyerahan'   => ['label' => 'Tempat/lokasi', 'rules' => 'required', 'errors' => ['required' => '{field} wajib diisi']],
            'berdasarkan'         => ['label' => 'Berdasarkan', 'rules' => 'required|in_list[Rapat Pengurus,Laporan/SPJ]', 'errors' => ['required' => '{field} wajib dipilih']],
            'nilai_penyerahan'    => ['label' => 'Nilai penyerahan', 'rules' => 'required|numeric', 'errors' => ['required' => '{field} wajib diisi']],
            'yang_menerima'       => ['label' => 'Yang menerima', 'rules' => 'required', 'errors' => ['required' => '{field} wajib diisi']],
        ];

        if (!$b3) {
            $valid['bentuk_penyerahan'] = ['label' => 'Bentuk penyerahan', 'rules' => 'required|numeric', 'errors' => ['required' => '{field} wajib dipilih']];
            $valid['dana_dari'] = ['label' => 'Sumber dana', 'rules' => 'required|in_list[Zakat,Infaq Umum,Amil,Infaq Terikat,DSKL]', 'errors' => ['required' => '{field} wajib dipilih']];
            $valid['kategori_penerima'] = ['label' => 'Asnaf/kategori penerima', 'rules' => 'required|numeric', 'errors' => ['required' => '{field} wajib dipilih']];
        }

        if (!$this->validate($valid)) {
            session()->setFlashdata('gagal', implode(' ', $this->validator->getErrors()));

            return redirect()->to(base_url('ajuan/' . $nomorAjuan));
        }

        $this->beritaAcaraModel->insert([
            'nomor_ajuan'         => $nomorAjuan,
            'yang_bertandatangan' => $this->request->getPost('yang_bertandatangan'),
            'tanggal_penyerahan'  => $this->request->getPost('tanggal_penyerahan'),
            'lokasi_penyerahan'   => $this->request->getPost('lokasi_penyerahan'),
            'berdasarkan'         => $this->request->getPost('berdasarkan'),
            'bentuk_penyerahan'   => $b3['bentuk_penyerahan'] ?? $this->request->getPost('bentuk_penyerahan'),
            'nilai_penyerahan'    => $this->request->getPost('nilai_penyerahan'),
            'rekening_penyerahan' => $this->request->getPost('rekening_penyerahan'),
            'nama_barang'         => $this->request->getPost('nama_barang'),
            'dana_dari'           => $b3['dana_dari'] ?? $this->request->getPost('dana_dari'),
            'kategori_penerima'   => $b3['kategori_penerima'] ?? $this->request->getPost('kategori_penerima'),
            'yang_menerima'       => $this->request->getPost('yang_menerima'),
        ]);

        session()->setFlashdata('berhasil', 'Berita acara berhasil disimpan!');

        return redirect()->to(base_url('ajuan/' . $nomorAjuan));
    }

    /** Attaches a proof/signed-copy file to an existing Berita Acara. */
    public function uploadBuktiBeritaAcara(int $idBeritaAcara)
    {
        $beritaAcara = $this->beritaAcaraModel->find($idBeritaAcara);

        if (!$beritaAcara) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $file = $this->moveUpload('file_berita_acara', 'berita_acara');

        if ($file) {
            $this->beritaAcaraModel->update($idBeritaAcara, ['file_berita_acara' => $file]);
            session()->setFlashdata('berhasil', 'Bukti berita acara berhasil diunggah!');
        } else {
            session()->setFlashdata('gagal', 'File bukti wajib diunggah!');
        }

        // Uploaded from either the ajuan detail modal or the Berita Acara
        // preview page, so return wherever the form was actually submitted from.
        return redirect()->back();
    }

    /** Streams a Berita Acara's uploaded proof file inline. */
    public function fileBeritaAcara(int $idBeritaAcara)
    {
        $beritaAcara = $this->beritaAcaraModel->find($idBeritaAcara);

        if (!$beritaAcara || !$beritaAcara['file_berita_acara']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $path = WRITEPATH . 'uploads/ajuan/berita_acara/' . $beritaAcara['file_berita_acara'];

        if (!is_file($path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->response
            ->setHeader('Content-Type', mime_content_type($path))
            ->setHeader('Content-Disposition', 'inline; filename="' . $beritaAcara['file_berita_acara'] . '"')
            ->setBody(file_get_contents($path));
    }

    public function deleteBeritaAcara(int $idBeritaAcara)
    {
        $beritaAcara = $this->beritaAcaraModel->find($idBeritaAcara);

        if (!$beritaAcara) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $ok = $this->beritaAcaraModel->delete($idBeritaAcara);

        session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Berita acara berhasil dihapus!' : 'GAGAL menghapus berita acara!');

        return redirect()->to(base_url('ajuan/' . $beritaAcara['nomor_ajuan']));
    }

    /** Printable PDF of a Berita Acara (handover minutes). */
    public function pdfBeritaAcara(int $idBeritaAcara)
    {
        $beritaAcara = $this->beritaAcaraModel
            ->select('ad_berita_acara.*, ktg.ket_kategori_penerima, bp.ket_bentuk_penyerahan')
            ->join('dt_kategori_penerima ktg', 'ktg.id_kategori_penerima = ad_berita_acara.kategori_penerima')
            ->join('dt_bentuk_penyerahan bp', 'bp.id_bentuk_penyerahan = ad_berita_acara.bentuk_penyerahan')
            ->where('id_berita_acara', $idBeritaAcara)
            ->first();

        if (!$beritaAcara) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $ajuan    = $this->ajuanModel->withRelasi()->where('tr_ajuan.nomor_ajuan', $beritaAcara['nomor_ajuan'])->first();
        $pemohon  = $this->pemohonModel->withWilayah()->where('tr_pemohon.nik', $ajuan['nik'])->first();
        $individu = $ajuan['jenis_ajuan'] === 'Individu'
            ? $this->individuModel->withMustahik()->where('tr_individu.nomor_ajuan', $beritaAcara['nomor_ajuan'])->first()
            : null;
        $lembaga  = $ajuan['jenis_ajuan'] === 'Lembaga'
            ? $this->lembagaModel->withLembaga()->where('tr_lembaga.nomor_ajuan', $beritaAcara['nomor_ajuan'])->first()
            : null;

        $rapatLog = $beritaAcara['berdasarkan'] === 'Rapat Pengurus'
            ? $this->logAjuanModel->where('nomor_ajuan', $beritaAcara['nomor_ajuan'])->where('status_ajuan', 3)->first()
            : null;

        $namaKadivProgram = (new JabatanPenjabatModel())
            ->join('jabatan', 'jabatan.id = jabatan_penjabat.id_jabatan')
            ->where('jabatan.kode_jabatan', 'kepala_program')
            ->orderBy('jabatan_penjabat.mulai_tahun', 'DESC')
            ->first()['nama_penjabat'] ?? '';

        $html = view('ajuan/pdf/berita_acara', [
            'beritaAcara'      => $beritaAcara,
            'ajuan'            => $ajuan,
            'pemohon'          => $pemohon,
            'individu'         => $individu,
            'lembaga'          => $lembaga,
            'tanggalRapat'     => $rapatLog['tanggal_log'] ?? null,
            'terbilang'        => terbilang((float) $beritaAcara['nilai_penyerahan']),
            'namaKadivProgram' => $namaKadivProgram,
        ]);

        $pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Lazismu Sragen');
        $pdf->SetTitle('Berita Acara ' . $idBeritaAcara);
        $pdf->SetSubject('Berita Acara ' . $idBeritaAcara);
        $pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');

        return $this->response
            ->setContentType('application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="Berita-Acara-' . $idBeritaAcara . '.pdf"')
            ->setBody($pdf->Output('Berita-Acara-' . $idBeritaAcara . '.pdf', 'S'));
    }

    /** Narrative on-screen preview of a Berita Acara (the same content as the PDF, browsable as a page). */
    public function previewBeritaAcara(int $idBeritaAcara)
    {
        $beritaAcara = $this->beritaAcaraModel
            ->select('ad_berita_acara.*, ktg.ket_kategori_penerima, bp.ket_bentuk_penyerahan')
            ->join('dt_kategori_penerima ktg', 'ktg.id_kategori_penerima = ad_berita_acara.kategori_penerima')
            ->join('dt_bentuk_penyerahan bp', 'bp.id_bentuk_penyerahan = ad_berita_acara.bentuk_penyerahan')
            ->where('id_berita_acara', $idBeritaAcara)
            ->first();

        if (!$beritaAcara) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $ajuan   = $this->ajuanModel->withRelasi()->where('tr_ajuan.nomor_ajuan', $beritaAcara['nomor_ajuan'])->first();
        $pemohon = $this->pemohonModel->withWilayah()->where('tr_pemohon.nik', $ajuan['nik'])->first();
        $lembaga = $ajuan['jenis_ajuan'] === 'Lembaga'
            ? $this->lembagaModel->withLembaga()->where('tr_lembaga.nomor_ajuan', $beritaAcara['nomor_ajuan'])->first()
            : null;

        $rapatLog = $beritaAcara['berdasarkan'] === 'Rapat Pengurus'
            ? $this->logAjuanModel->where('nomor_ajuan', $beritaAcara['nomor_ajuan'])->where('status_ajuan', 3)->first()
            : null;

        return view('ajuan/berita_acara_preview', [
            'title'        => 'Preview Berita Acara ' . $beritaAcara['nomor_ajuan'],
            'activeMenu'   => $ajuan['jenis_ajuan'] === 'Individu' ? 'ajuan-individu' : 'ajuan-lembaga',
            'beritaAcara'  => $beritaAcara,
            'ajuan'        => $ajuan,
            'pemohon'      => $pemohon,
            'lembaga'      => $lembaga,
            'tanggalRapat' => $rapatLog['tanggal_log'] ?? null,
        ]);
    }

    /** Printable C17 kuitansi (payment receipt) for a Berita Acara's disbursement. */
    public function pdfC17Kwitansi(int $idBeritaAcara)
    {
        $beritaAcara = $this->beritaAcaraModel
            ->select('ad_berita_acara.*, ktg.ket_kategori_penerima, bp.ket_bentuk_penyerahan')
            ->join('dt_kategori_penerima ktg', 'ktg.id_kategori_penerima = ad_berita_acara.kategori_penerima')
            ->join('dt_bentuk_penyerahan bp', 'bp.id_bentuk_penyerahan = ad_berita_acara.bentuk_penyerahan')
            ->where('id_berita_acara', $idBeritaAcara)
            ->first();

        if (!$beritaAcara) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $ajuan    = $this->ajuanModel->withRelasi()->where('tr_ajuan.nomor_ajuan', $beritaAcara['nomor_ajuan'])->first();
        $individu = $ajuan['jenis_ajuan'] === 'Individu'
            ? $this->individuModel->withMustahik()->where('tr_individu.nomor_ajuan', $beritaAcara['nomor_ajuan'])->first()
            : null;
        $lembaga = $ajuan['jenis_ajuan'] === 'Lembaga'
            ? $this->lembagaModel->withLembaga()->where('tr_lembaga.nomor_ajuan', $beritaAcara['nomor_ajuan'])->first()
            : null;

        $b3 = $this->formB3Model
            ->select('tr_form_b3.*, prg.nama_program, kp.nama_kategori')
            ->join('ad_program prg', 'prg.id_program = tr_form_b3.id_program', 'left')
            ->join('ad_kategori_program kp', 'kp.id_kategori_program = tr_form_b3.id_kategori_program', 'left')
            ->where('tr_form_b3.nomor_ajuan', $beritaAcara['nomor_ajuan'])
            ->first();

        $penjabatModel    = new JabatanPenjabatModel();
        $namaManajer      = $penjabatModel
            ->join('jabatan', 'jabatan.id = jabatan_penjabat.id_jabatan')
            ->where('jabatan.kode_jabatan', 'manajer')
            ->orderBy('jabatan_penjabat.mulai_tahun', 'DESC')
            ->first()['nama_penjabat'] ?? '';
        $namaKadivProgram = $penjabatModel
            ->join('jabatan', 'jabatan.id = jabatan_penjabat.id_jabatan')
            ->where('jabatan.kode_jabatan', 'kepala_program')
            ->orderBy('jabatan_penjabat.mulai_tahun', 'DESC')
            ->first()['nama_penjabat'] ?? '';

        $html = view('ajuan/pdf/c17_kwitansi', [
            'beritaAcara' => $beritaAcara,
            'ajuan'       => $ajuan,
            'individu'    => $individu,
            'lembaga'     => $lembaga,
            'b3'          => $b3,
            'terbilang'   => terbilang((float) $beritaAcara['nilai_penyerahan']),
            'nilai_penyerahan' => (float) $beritaAcara['nilai_penyerahan'],
            'nilai_disetujui' => (float) $ajuan['nilai_disetujui'],
            'namaManajer'      => $namaManajer,
            'namaKadivProgram' => $namaKadivProgram,
        ]);

        $pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Lazismu Sragen');
        $pdf->SetTitle('C17 Kwitansi ' . $idBeritaAcara);
        $pdf->SetSubject('C17 Kwitansi ' . $idBeritaAcara);
        $pdf->SetMargins(3, 4, 3);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');

        return $this->response
            ->setContentType('application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="C17-Kwitansi-' . $idBeritaAcara . '.pdf"')
            ->setBody($pdf->Output('C17-Kwitansi-' . $idBeritaAcara . '.pdf', 'S'));
    }

    /**
     * Excel export of the C1 "Permohonan Pencairan Dana" form for a berita
     * acara's disbursement. Uses the DIATAS/DIBAWAH 5 juta template
     * depending on the ajuan's nilai_disetujui (same threshold the
     * disposisi workflow uses for routing to Badan Pengurus).
     */
    public function downloadC1(int $idBeritaAcara)
    {
        $beritaAcara = $this->beritaAcaraModel->find($idBeritaAcara);

        if (!$beritaAcara) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $ajuan = $this->ajuanModel->withRelasi()->where('tr_ajuan.nomor_ajuan', $beritaAcara['nomor_ajuan'])->first();

        $individu = $ajuan['jenis_ajuan'] === 'Individu'
            ? $this->individuModel->withMustahik()->where('tr_individu.nomor_ajuan', $beritaAcara['nomor_ajuan'])->first()
            : null;
        $lembaga = $ajuan['jenis_ajuan'] === 'Lembaga'
            ? $this->lembagaModel->withLembaga()->where('tr_lembaga.nomor_ajuan', $beritaAcara['nomor_ajuan'])->first()
            : null;
        $namaPenerima  = $individu['nama_mustahik'] ?? $lembaga['nama_lembaga'] ?? '-';
        $alamatPenerima = $individu['alamat'] ?? $lembaga['alamat_lembaga'] ?? '-';

        // The "PROGRAM" / "Golongan Asnaf" columns use Form B3's classification
        // (kategori program / pilar) rather than the ajuan's own initial
        // id_program, matching how the C17 kuitansi already sources its
        // Program field — B3 reflects what was actually disbursed.
        $b3 = $this->formB3Model
            ->select('tr_form_b3.*, kp.nama_kategori, pl.nama_pilar')
            ->join('ad_kategori_program kp', 'kp.id_kategori_program = tr_form_b3.id_kategori_program', 'left')
            ->join('dt_pilar pl', 'pl.id_pilar = kp.id_pilar', 'left')
            ->where('tr_form_b3.nomor_ajuan', $beritaAcara['nomor_ajuan'])
            ->first();

        $namaProgram = $b3['nama_kategori'] ?? $ajuan['nama_program'] ?? '-';
        $namaPilar   = $b3['nama_pilar'] ?? '-';

        $penjabatModel = new JabatanPenjabatModel();
        $cariPenjabat  = static fn (string $kode): string => $penjabatModel
            ->join('jabatan', 'jabatan.id = jabatan_penjabat.id_jabatan')
            ->where('jabatan.kode_jabatan', $kode)
            ->orderBy('jabatan_penjabat.mulai_tahun', 'DESC')
            ->first()['nama_penjabat'] ?? '';

        $namaKepalaProgram  = $cariPenjabat('kepala_program');
        $namaKepalaKeuangan = $cariPenjabat('kepala_keuangan');
        $namaManajer        = $cariPenjabat('manajer');
        $namaKetuaBadan     = $cariPenjabat('ketua_badan');

        $nilaiDisetujui = (float) $ajuan['nilai_disetujui'];
        $besar          = $nilaiDisetujui > self::AMBANG_C1_BESAR;

        $bulanIndo    = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $ts           = strtotime((string) $ajuan['tgl_diajukan']) ?: time();
        $namaBulan    = $bulanIndo[(int) date('n', $ts)];
        $tahun        = date('Y', $ts);
        $periode      = 'PERIODE ' . mb_strtoupper($namaBulan) . ' ' . $tahun;
        $tglPengajuan = sprintf('%02d %s %s', (int) date('j', $ts), $namaBulan, $tahun);

        // "Pencairan Dana Via" has no bank-name field anywhere in the schema
        // (rekening_penyerahan is just an account number) — best available
        // substitute is the fund source plus that account number.
        $pencairanVia = (string) ($beritaAcara['dana_dari'] ?? '');
        if (!empty($beritaAcara['rekening_penyerahan'])) {
            $pencairanVia .= ' - ' . $beritaAcara['rekening_penyerahan'];
        }

        $templatePath = APPPATH . 'Resources/templates/' . ($besar ? 'c1_diatas_5jt.xlsx' : 'c1_dibawah_5jt.xlsx');
        $spreadsheet  = IOFactory::load($templatePath);
        $sheet        = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A2', $periode);
        $sheet->setCellValue('A4', 'Pemohon       : ' . $namaKepalaProgram);
        $sheet->setCellValue('G4', $ajuan['nomor_ajuan']);
        $sheet->setCellValue('G5', $tglPengajuan);

        if ($besar) {
            $sheet->setCellValue('A7', $alamatPenerima);
            $sheet->setCellValue('B9', $namaPenerima);
            $sheet->setCellValue('D9', $namaProgram);
            $sheet->setCellValue('F9', $nilaiDisetujui);
            $sheet->setCellValue('D24', $pencairanVia);
            $sheet->setCellValue('D25', $namaPilar);
            $sheet->setCellValue('A33', $namaKetuaBadan);
            $sheet->setCellValue('C33', $namaManajer);
            $sheet->setCellValue('E33', $namaKepalaKeuangan);
            $sheet->setCellValue('G33', $namaKepalaProgram);
            $sheet->getCell('F20')->getCalculatedValue();
            $sheet->getCell('C22')->getCalculatedValue();
        } else {
            $sheet->setCellValue('A8', $alamatPenerima);
            $sheet->setCellValue('B10', $namaPenerima);
            $sheet->setCellValue('D10', $namaProgram);
            $sheet->setCellValue('F10', $nilaiDisetujui);
            $sheet->setCellValue('D25', $pencairanVia);
            $sheet->setCellValue('D26', $namaPilar);
            $sheet->setCellValue('A34', $namaManajer);
            $sheet->setCellValue('D34', $namaKepalaKeuangan);
            $sheet->setCellValue('G34', $namaKepalaProgram);
            $sheet->getCell('F21')->getCalculatedValue();
            $sheet->getCell('C23')->getCalculatedValue();
        }

        $filename = 'C1-' . $ajuan['nomor_ajuan'] . '.xlsx';

        ob_start();
        IOFactory::createWriter($spreadsheet, 'Xlsx')->save('php://output');
        $content = ob_get_clean();

        return $this->response
            ->setContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($content);
    }

    public function updateMustahik(string $nomorAjuan)
    {
        $individu = $this->individuModel->where('nomor_ajuan', $nomorAjuan)->first();

        if (!$individu) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $valid = [
            'nama_mustahik'    => ['label' => 'Nama', 'rules' => 'required', 'errors' => ['required' => '{field} wajib diisi']],
            'kelamin_mustahik' => ['label' => 'Jenis kelamin', 'rules' => 'required', 'errors' => ['required' => '{field} wajib dipilih']],
            'dusun'            => ['label' => 'Dusun/Nama Jalan', 'rules' => 'required', 'errors' => ['required' => '{field} wajib diisi']],
        ];

        if (!$this->validate($valid)) {
            session()->setFlashdata('gagal', implode(' ', $this->validator->getErrors()));

            return redirect()->to(base_url('ajuan/' . $nomorAjuan));
        }

        // tr_individu only links to the shared ms_individu master profile, so
        // editing here updates that master record (nik is the FK/PK).
        $ok = $this->masterIndividuModel->update($individu['nik'], [
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
            'status_pendidikan' => $this->request->getPost('status_pendidikan'),
            'status_marital'    => $this->request->getPost('status_marital'),
            'jml_keluarga'      => $this->request->getPost('jml_keluarga') ?: null,
            'no_handphone'      => $this->request->getPost('no_handphone'),
            'email'             => $this->request->getPost('email'),
        ]);

        session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Data mustahik berhasil diperbarui!' : 'GAGAL memperbarui data mustahik!');

        return redirect()->to(base_url('ajuan/' . $nomorAjuan));
    }

    /** Streams the mustahik's foto_ktp/foto_kk inline so admins can view it in a new tab. */
    public function dokumenMustahik(string $nomorAjuan, string $jenis)
    {
        if (!in_array($jenis, ['ktp', 'kk'], true)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $individu = $this->individuModel->withMustahik()->where('tr_individu.nomor_ajuan', $nomorAjuan)->first();
        $filename = $individu[$jenis === 'ktp' ? 'foto_ktp' : 'foto_kk'] ?? null;

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

    public function storeDokumentasi(string $nomorAjuan)
    {
        $namaFile = $this->moveUpload('nama_file', 'dokumentasi');

        if ($namaFile) {
            $this->dokumentasiModel->simpan($nomorAjuan, $namaFile, 'menunggu', $this->request->getPost('catatan'));
            session()->setFlashdata('berhasil', 'Dokumentasi berhasil diunggah!');
        } else {
            session()->setFlashdata('gagal', 'File dokumentasi wajib diunggah!');
        }

        return redirect()->to(base_url('ajuan/' . $nomorAjuan));
    }

    public function storeKuitansi(string $nomorAjuan)
    {
        $namaFile = $this->moveUpload('nama', 'kuitansi');

        if ($namaFile) {
            $this->kuitansiModel->simpan($nomorAjuan, $namaFile, 'menunggu', $this->request->getPost('catatan'));
            session()->setFlashdata('berhasil', 'Kuitansi berhasil diunggah!');
        } else {
            session()->setFlashdata('gagal', 'File kuitansi wajib diunggah!');
        }

        return redirect()->to(base_url('ajuan/' . $nomorAjuan));
    }

    public function delete(string $nomorAjuan)
    {
        // tr_lembaga, tr_dokumentasi, tr_kuitansi, tr_form_b3 and ad_log_ajuan
        // cascade automatically via FK; tr_individu has no FK (matching the
        // source schema) so it's cleaned up explicitly here.
        $this->individuModel->where('nomor_ajuan', $nomorAjuan)->delete();

        $ok = $this->ajuanModel->where('nomor_ajuan', $nomorAjuan)->delete();

        session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Ajuan berhasil dihapus!' : 'GAGAL menghapus ajuan!');

        return redirect()->to(base_url('ajuan'));
    }

    /**
     * Validates and moves an uploaded file into writable/uploads/ajuan/{subdir}.
     * Returns the stored filename, or null if no (valid) file was uploaded.
     */
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
