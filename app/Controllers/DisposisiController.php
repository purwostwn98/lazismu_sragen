<?php

namespace App\Controllers;

use App\Models\AjuanModel;
use App\Models\DisposisiModel;
use App\Models\IndividuModel;
use App\Models\LembagaModel;
use App\Models\LogAjuanModel;
use CodeIgniter\HTTP\Files\UploadedFile;

/**
 * Ajuan disposisi workflow: Surveyor -> Kepala Divisi Program -> Manager ->
 * Badan Pengurus (last stage only for ajuan with nilai_diajukan > 5,000,000).
 * Each stage reviews the same ajuan and writes its own ad_disposisi row
 * (upserted, one current result per ajuan per stage — see submitDisposisi()).
 */
class DisposisiController extends BaseController
{
    private const AMBANG_BADAN_PENGURUS = 5000000;

    protected AjuanModel $ajuanModel;
    protected IndividuModel $individuModel;
    protected LembagaModel $lembagaModel;
    protected DisposisiModel $disposisiModel;
    protected LogAjuanModel $logAjuanModel;

    public function __construct()
    {
        $this->ajuanModel     = new AjuanModel();
        $this->individuModel  = new IndividuModel();
        $this->lembagaModel   = new LembagaModel();
        $this->disposisiModel = new DisposisiModel();
        $this->logAjuanModel  = new LogAjuanModel();
    }

    /** Dashboard: how many ajuan are pending/done at each disposisi stage, recommendation breakdowns, and recent activity. */
    public function dashboard()
    {
        $dari   = $this->request->getGet('dari') ?: null;
        $sampai = $this->request->getGet('sampai') ?: null;

        $stages = ['Surveyor', 'Kepala Divisi Program', 'Manager', 'Badan Pengurus'];

        $nomorPerStage = [];
        foreach ($stages as $stage) {
            $nomorPerStage[$stage] = $this->nomorAjuanWithDisposisi($stage);
        }

        $totalDalamAlur = $this->ajuanModel->where('status_ajuan <=', 5)->countAllResults();

        $belumDisurvey = $nomorPerStage['Surveyor'] === []
            ? $totalDalamAlur
            : $this->ajuanModel->where('status_ajuan <=', 5)->whereNotIn('nomor_ajuan', $nomorPerStage['Surveyor'])->countAllResults();

        $menungguKadiv   = count(array_diff($nomorPerStage['Surveyor'], $nomorPerStage['Kepala Divisi Program']));
        $menungguManager = count(array_diff($nomorPerStage['Kepala Divisi Program'], $nomorPerStage['Manager']));

        // Ajuan yang sudah ditinjau Manager dan nilainya di atas ambang wajib lanjut ke Badan Pengurus.
        $perluBadanPengurus = $nomorPerStage['Manager'] === []
            ? []
            : ($this->ajuanModel->select('nomor_ajuan')
                ->whereIn('nomor_ajuan', $nomorPerStage['Manager'])
                ->where('nilai_diajukan >', self::AMBANG_BADAN_PENGURUS)
                ->findColumn('nomor_ajuan') ?? []);

        $menungguBadanPengurus = count(array_diff($perluBadanPengurus, $nomorPerStage['Badan Pengurus']));
        $selesaiTanpaBadan     = count($nomorPerStage['Manager']) - count($perluBadanPengurus);
        $selesaiDenganBadan    = count(array_intersect($perluBadanPengurus, $nomorPerStage['Badan Pengurus']));

        // Rekap rekomendasi (disetujui/ditolak) dan jumlah diproses per tahap,
        // dari hasil terbaru masing-masing ajuan yang aktivitasnya jatuh di
        // rentang tanggal filter (default: sepanjang waktu). Ini terpisah
        // dari angka backlog di atas (yang selalu menggambarkan kondisi
        // saat ini, tidak masuk akal untuk difilter tanggal).
        $rekapTahap     = [];
        $jumlahPerTahap = [];
        foreach ($stages as $stage) {
            $rekap                    = $this->rekapTahapTerfilter($stage, $dari, $sampai);
            $jumlahPerTahap[$stage]   = $rekap['jumlah'];
            $rekapTahap[$stage]       = ['disetujui' => $rekap['disetujui'], 'ditolak' => $rekap['ditolak'], 'nominal' => $rekap['nominal']];
        }

        $aktivitasQuery = $this->disposisiModel
            ->select('ad_disposisi.id, ad_disposisi.nomor_ajuan, ad_disposisi.oleh, ad_disposisi.rekomendasi, ad_disposisi.nominal_rekomendasi, ad_disposisi.nama_petugas, ad_disposisi.created_at, tr_pemohon.nama_pemohon, ad_program.nama_program')
            ->join('tr_ajuan', 'tr_ajuan.nomor_ajuan = ad_disposisi.nomor_ajuan', 'left')
            ->join('tr_pemohon', 'tr_pemohon.nik = tr_ajuan.nik', 'left')
            ->join('ad_program', 'ad_program.id_program = tr_ajuan.id_program', 'left');

        if ($dari) {
            $aktivitasQuery->where('ad_disposisi.created_at >=', $dari);
        }
        if ($sampai) {
            $aktivitasQuery->where('ad_disposisi.created_at <=', $sampai . ' 23:59:59');
        }

        $aktivitasTerbaru = $aktivitasQuery->orderBy('ad_disposisi.created_at', 'DESC')->limit(10)->findAll();

        return view('disposisi/dashboard', [
            'title'                 => 'Dashboard Disposisi',
            'activeMenu'            => 'dashboard-disposisi',
            'dari'                  => $dari,
            'sampai'                => $sampai,
            'totalDalamAlur'        => $totalDalamAlur,
            'belumDisurvey'         => $belumDisurvey,
            'sudahDisurvey'         => count($nomorPerStage['Surveyor']),
            'menungguKadiv'         => $menungguKadiv,
            'menungguManager'       => $menungguManager,
            'perluBadanPengurus'    => count($perluBadanPengurus),
            'menungguBadanPengurus' => $menungguBadanPengurus,
            'selesaiTanpaBadan'     => $selesaiTanpaBadan,
            'selesaiDenganBadan'    => $selesaiDenganBadan,
            'jumlahPerTahap'        => $jumlahPerTahap,
            'rekapTahap'            => $rekapTahap,
            'aktivitasTerbaru'      => $aktivitasTerbaru,
        ]);
    }

    /**
     * Jumlah diproses + rekap rekomendasi untuk satu tahap, dari entri
     * ad_disposisi terbaru per ajuan yang created_at-nya jatuh di rentang
     * dari-sampai (keduanya opsional).
     */
    private function rekapTahapTerfilter(string $oleh, ?string $dari, ?string $sampai): array
    {
        $query = $this->disposisiModel->where('oleh', $oleh);

        if ($dari) {
            $query->where('created_at >=', $dari);
        }
        if ($sampai) {
            $query->where('created_at <=', $sampai . ' 23:59:59');
        }

        $terkiniPerAjuan = [];
        foreach ($query->orderBy('created_at', 'DESC')->findAll() as $row) {
            $terkiniPerAjuan[$row['nomor_ajuan']] ??= $row;
        }

        $disetujui = 0;
        $ditolak   = 0;
        $nominal   = 0.0;
        foreach ($terkiniPerAjuan as $d) {
            if ((int) $d['rekomendasi'] === 1) {
                $disetujui++;
                $nominal += (float) $d['nominal_rekomendasi'];
            } else {
                $ditolak++;
            }
        }

        return ['jumlah' => count($terkiniPerAjuan), 'disetujui' => $disetujui, 'ditolak' => $ditolak, 'nominal' => $nominal];
    }

    /** Worklist for the Surveyor: ajuan still at or before the Survey stage. */
    public function surveyor()
    {
        $rows = $this->ajuanModel->withRelasi()
            ->where('tr_ajuan.status_ajuan <=', 5)
            ->orderBy('tgl_diajukan', 'DESC')
            ->findAll();

        $sudahDisurvey = $this->nomorAjuanWithDisposisi('Surveyor');

        foreach ($rows as &$row) {
            $row['sudah_disurvey'] = in_array($row['nomor_ajuan'], $sudahDisurvey, true);
        }
        unset($row);

        return view('disposisi/surveyor', [
            'title'      => 'Disposisi Surveyor',
            'activeMenu' => 'surveyor',
            'rows'       => $rows,
        ]);
    }

    /** Focused survey page: ajuan info, mustahik/lembaga data, and the survey result form. */
    public function survey(string $nomorAjuan)
    {
        [$ajuan, $individu, $lembaga] = $this->ajuanDenganMustahik($nomorAjuan);

        $riwayatSurvey = $this->disposisiModel
            ->where('nomor_ajuan', $nomorAjuan)
            ->where('oleh', 'Surveyor')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('disposisi/survey', [
            'title'         => 'Survey Ajuan ' . $nomorAjuan,
            'activeMenu'    => 'surveyor',
            'ajuan'         => $ajuan,
            'individu'      => $individu,
            'lembaga'       => $lembaga,
            'riwayatSurvey' => $riwayatSurvey,
            'latestSurvey'  => $riwayatSurvey[0] ?? null,
        ]);
    }

    /** Saves a Surveyor's survey result (deskripsi, dokumentasi, rekomendasi) for an ajuan. */
    public function storeSurvey(string $nomorAjuan)
    {
        return $this->submitDisposisi(
            $nomorAjuan,
            'Surveyor',
            base_url('disposisi/survey/' . $nomorAjuan),
            function (array $ajuan) use ($nomorAjuan) {
                // Submitting a survey result advances the ajuan to the "Survey" stage.
                if ((int) $ajuan['status_ajuan'] !== 5) {
                    $this->ajuanModel->where('nomor_ajuan', $nomorAjuan)->set(['status_ajuan' => 5])->update();
                    $this->logAjuanModel->catat($nomorAjuan, 5, 'Hasil survey disimpan oleh ' . (session()->get('nama') ?? 'Surveyor'));
                }
            }
        );
    }

    /** Worklist for Kepala Divisi Program: ajuan the Surveyor has already surveyed. */
    public function kepalaDivisiProgram()
    {
        $rows = $this->worklistUntuk('Surveyor');

        $surveyByAjuan = $this->disposisiMapFor(array_column($rows, 'nomor_ajuan'), 'Surveyor');
        foreach ($rows as &$row) {
            $row['survey'] = $surveyByAjuan[$row['nomor_ajuan']] ?? null;
        }
        unset($row);

        return view('disposisi/kepala_divisi_program', [
            'title'      => 'Disposisi Kepala Divisi Program',
            'activeMenu' => 'kepala-divisi-program',
            'rows'       => $rows,
        ]);
    }

    /** Review page for Kepala Divisi Program: ajuan info, mustahik/lembaga data, Surveyor's result, and the review form. */
    public function reviewKepalaDivisiProgram(string $nomorAjuan)
    {
        [$ajuan, $individu, $lembaga] = $this->ajuanDenganMustahik($nomorAjuan);

        $survey       = $this->findDisposisi($nomorAjuan, 'Surveyor');
        $riwayatKadiv = $this->disposisiModel->where('nomor_ajuan', $nomorAjuan)->where('oleh', 'Kepala Divisi Program')->orderBy('created_at', 'DESC')->findAll();

        return view('disposisi/kepala_divisi_program_detail', [
            'title'        => 'Tinjau Ajuan ' . $nomorAjuan,
            'activeMenu'   => 'kepala-divisi-program',
            'ajuan'        => $ajuan,
            'individu'     => $individu,
            'lembaga'      => $lembaga,
            'survey'       => $survey,
            'riwayatKadiv' => $riwayatKadiv,
            'latestKadiv'  => $riwayatKadiv[0] ?? null,
        ]);
    }

    /** Saves Kepala Divisi Program's review (deskripsi, dokumentasi, rekomendasi) for an ajuan. */
    public function storeKepalaDivisiProgram(string $nomorAjuan)
    {
        return $this->submitDisposisi($nomorAjuan, 'Kepala Divisi Program', base_url('disposisi/kepala-divisi-program/' . $nomorAjuan));
    }

    /** Worklist for Manager: ajuan Kepala Divisi Program has already reviewed. */
    public function manager()
    {
        $rows = $this->worklistUntuk('Kepala Divisi Program');

        $kadivByAjuan = $this->disposisiMapFor(array_column($rows, 'nomor_ajuan'), 'Kepala Divisi Program');
        foreach ($rows as &$row) {
            $row['kadiv'] = $kadivByAjuan[$row['nomor_ajuan']] ?? null;
        }
        unset($row);

        return view('disposisi/manager', [
            'title'      => 'Disposisi Manager',
            'activeMenu' => 'manager',
            'rows'       => $rows,
        ]);
    }

    /** Review page for Manager: ajuan info, mustahik/lembaga data, prior results, and the review form. */
    public function reviewManager(string $nomorAjuan)
    {
        [$ajuan, $individu, $lembaga] = $this->ajuanDenganMustahik($nomorAjuan);

        $survey         = $this->findDisposisi($nomorAjuan, 'Surveyor');
        $kadiv          = $this->findDisposisi($nomorAjuan, 'Kepala Divisi Program');
        $riwayatManager = $this->disposisiModel->where('nomor_ajuan', $nomorAjuan)->where('oleh', 'Manager')->orderBy('created_at', 'DESC')->findAll();

        return view('disposisi/manager_detail', [
            'title'          => 'Tinjau Ajuan ' . $nomorAjuan,
            'activeMenu'     => 'manager',
            'ajuan'          => $ajuan,
            'individu'       => $individu,
            'lembaga'        => $lembaga,
            'survey'         => $survey,
            'kadiv'          => $kadiv,
            'riwayatManager' => $riwayatManager,
            'latestManager'  => $riwayatManager[0] ?? null,
        ]);
    }

    /** Saves Manager's review (deskripsi, dokumentasi, rekomendasi) for an ajuan. */
    public function storeManager(string $nomorAjuan)
    {
        return $this->submitDisposisi($nomorAjuan, 'Manager', base_url('disposisi/manager/' . $nomorAjuan));
    }

    /** Worklist for Badan Pengurus: ajuan Manager has reviewed AND nilai_diajukan > 5,000,000. */
    public function badanPengurus()
    {
        $rows = $this->worklistUntuk('Manager', static function ($query) {
            $query->where('tr_ajuan.nilai_diajukan >', self::AMBANG_BADAN_PENGURUS);
        });

        $managerByAjuan = $this->disposisiMapFor(array_column($rows, 'nomor_ajuan'), 'Manager');
        foreach ($rows as &$row) {
            $row['manager'] = $managerByAjuan[$row['nomor_ajuan']] ?? null;
        }
        unset($row);

        return view('disposisi/badan_pengurus', [
            'title'      => 'Disposisi Badan Pengurus',
            'activeMenu' => 'badan-pengurus',
            'rows'       => $rows,
        ]);
    }

    /** Review page for Badan Pengurus: ajuan info, mustahik/lembaga data, prior results, and the review form. */
    public function reviewBadanPengurus(string $nomorAjuan)
    {
        [$ajuan, $individu, $lembaga] = $this->ajuanDenganMustahik($nomorAjuan);

        $survey       = $this->findDisposisi($nomorAjuan, 'Surveyor');
        $kadiv        = $this->findDisposisi($nomorAjuan, 'Kepala Divisi Program');
        $manager      = $this->findDisposisi($nomorAjuan, 'Manager');
        $riwayatBadan = $this->disposisiModel->where('nomor_ajuan', $nomorAjuan)->where('oleh', 'Badan Pengurus')->orderBy('created_at', 'DESC')->findAll();

        return view('disposisi/badan_pengurus_detail', [
            'title'        => 'Tinjau Ajuan ' . $nomorAjuan,
            'activeMenu'   => 'badan-pengurus',
            'ajuan'        => $ajuan,
            'individu'     => $individu,
            'lembaga'      => $lembaga,
            'survey'       => $survey,
            'kadiv'        => $kadiv,
            'manager'      => $manager,
            'riwayatBadan' => $riwayatBadan,
            'latestBadan'  => $riwayatBadan[0] ?? null,
        ]);
    }

    /** Saves Badan Pengurus's review (deskripsi, dokumentasi, rekomendasi) for an ajuan. */
    public function storeBadanPengurus(string $nomorAjuan)
    {
        return $this->submitDisposisi($nomorAjuan, 'Badan Pengurus', base_url('disposisi/badan-pengurus/' . $nomorAjuan));
    }

    /** Streams an uploaded disposisi dokumentasi file (PDF/JPEG) inline. */
    public function fileDokumentasi(int $id)
    {
        $disposisi = $this->disposisiModel->find($id);

        if (!$disposisi || !$disposisi['dokumentasi']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $path = WRITEPATH . 'uploads/disposisi/' . $disposisi['dokumentasi'];

        if (!is_file($path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->response
            ->setHeader('Content-Type', mime_content_type($path))
            ->setHeader('Content-Disposition', 'inline; filename="' . $disposisi['dokumentasi'] . '"')
            ->setBody(file_get_contents($path));
    }

    /** Fetches an ajuan plus its individu/lembaga data, 404-ing if the ajuan doesn't exist. */
    private function ajuanDenganMustahik(string $nomorAjuan): array
    {
        $ajuan = $this->ajuanModel->withRelasi()->where('tr_ajuan.nomor_ajuan', $nomorAjuan)->first();

        if (!$ajuan) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $individu = $ajuan['jenis_ajuan'] === 'Individu'
            ? $this->individuModel->withMustahik()->where('tr_individu.nomor_ajuan', $nomorAjuan)->first()
            : null;
        $lembaga = $ajuan['jenis_ajuan'] === 'Lembaga'
            ? $this->lembagaModel->withLembaga()->where('tr_lembaga.nomor_ajuan', $nomorAjuan)->first()
            : null;

        return [$ajuan, $individu, $lembaga];
    }

    /** All ajuan that have at least one ad_disposisi entry from the given stage, optionally narrowed further. */
    private function worklistUntuk(string $prasyaratOleh, ?callable $narrowQuery = null): array
    {
        $nomorList = $this->nomorAjuanWithDisposisi($prasyaratOleh);

        if ($nomorList === []) {
            return [];
        }

        $query = $this->ajuanModel->withRelasi()->whereIn('tr_ajuan.nomor_ajuan', $nomorList);

        if ($narrowQuery) {
            $narrowQuery($query);
        }

        return $query->orderBy('tgl_diajukan', 'DESC')->findAll();
    }

    /** Distinct nomor_ajuan that already have an ad_disposisi entry from the given stage. */
    private function nomorAjuanWithDisposisi(string $oleh): array
    {
        return $this->disposisiModel->select('nomor_ajuan')->where('oleh', $oleh)->groupBy('nomor_ajuan')->findColumn('nomor_ajuan') ?? [];
    }

    /** The single most recent ad_disposisi entry for (nomor_ajuan, oleh), or null. */
    private function findDisposisi(string $nomorAjuan, string $oleh): ?array
    {
        return $this->disposisiModel->where('nomor_ajuan', $nomorAjuan)->where('oleh', $oleh)->orderBy('created_at', 'DESC')->first();
    }

    /** Maps nomor_ajuan => its most recent ad_disposisi entry from the given stage, for a set of ajuan. */
    private function disposisiMapFor(array $nomorAjuanList, string $oleh): array
    {
        if ($nomorAjuanList === []) {
            return [];
        }

        $map = [];
        foreach ($this->disposisiModel->whereIn('nomor_ajuan', $nomorAjuanList)->where('oleh', $oleh)->orderBy('created_at', 'DESC')->findAll() as $row) {
            $map[$row['nomor_ajuan']] ??= $row;
        }

        return $map;
    }

    /**
     * Shared submit handler for every disposisi stage (Surveyor, Kepala
     * Divisi Program, Manager, Badan Pengurus): validates the review form,
     * upserts the ad_disposisi row scoped to ($nomorAjuan, $oleh) so an edit
     * updates the existing entry instead of piling up a new one, and runs
     * $onSuccess(array $ajuan) for any stage-specific side effect (e.g. the
     * Surveyor stage bumping status_ajuan).
     */
    private function submitDisposisi(string $nomorAjuan, string $oleh, string $redirectUrl, ?callable $onSuccess = null)
    {
        $ajuan = $this->ajuanModel->where('nomor_ajuan', $nomorAjuan)->first();

        if (!$ajuan) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $valid = [
            'deskripsi' => [
                'label'  => 'Deskripsi hasil tinjauan',
                'rules'  => 'required',
                'errors' => ['required' => '{field} wajib diisi'],
            ],
            'dokumentasi' => [
                'label'  => 'Dokumentasi',
                'rules'  => 'permit_empty|max_size[dokumentasi,2048]|ext_in[dokumentasi,pdf,jpg,jpeg]|mime_in[dokumentasi,application/pdf,image/jpeg,image/jpg]',
                'errors' => [
                    'max_size' => '{field} maksimal 2 MB',
                    'ext_in'   => '{field} harus berformat PDF atau JPEG',
                    'mime_in'  => '{field} harus berformat PDF atau JPEG',
                ],
            ],
            'rekomendasi' => [
                'label'  => 'Rekomendasi',
                'rules'  => 'required|in_list[0,1]',
                'errors' => ['required' => '{field} wajib dipilih', 'in_list' => '{field} tidak valid'],
            ],
            'nominal_rekomendasi' => [
                'label'  => 'Nominal rekomendasi',
                'rules'  => 'permit_empty|numeric',
                'errors' => ['numeric' => '{field} harus angka'],
            ],
        ];

        if (!$this->validate($valid)) {
            session()->setFlashdata('gagal', implode(' ', $this->validator->getErrors()));

            return redirect()->to($redirectUrl);
        }

        $rekomendasi = (int) $this->request->getPost('rekomendasi');
        $nominal     = (float) $this->request->getPost('nominal_rekomendasi');

        if ($rekomendasi === 1 && $nominal <= 0) {
            session()->setFlashdata('gagal', 'Nominal rekomendasi wajib diisi ketika rekomendasi disetujui.');

            return redirect()->to($redirectUrl);
        }

        // Editing an existing entry updates that same row instead of piling
        // up a new one, so each stage keeps a single current result per
        // ajuan (the "id" field is only present when the form was opened
        // via the Edit button).
        $id       = (int) $this->request->getPost('id');
        $existing = $id > 0
            ? $this->disposisiModel->where('id', $id)->where('nomor_ajuan', $nomorAjuan)->where('oleh', $oleh)->first()
            : null;

        $dokumentasi = $this->moveUpload('dokumentasi') ?? ($existing['dokumentasi'] ?? null);

        $data = [
            'nomor_ajuan'         => $nomorAjuan,
            'deskripsi'           => $this->sanitizeDeskripsi((string) $this->request->getPost('deskripsi')),
            'dokumentasi'         => $dokumentasi,
            'rekomendasi'         => $rekomendasi,
            'nominal_rekomendasi' => $rekomendasi === 1 ? $nominal : 0,
            'oleh'                => $oleh,
            'nama_petugas'        => session()->get('nama'),
        ];

        $ok = $existing
            ? $this->disposisiModel->update($existing['id'], $data)
            : $this->disposisiModel->insert($data) !== false;

        if ($ok && $onSuccess) {
            $onSuccess($ajuan);
        }

        session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'Hasil tinjauan berhasil disimpan!' : 'GAGAL menyimpan hasil tinjauan!');

        return redirect()->to($redirectUrl);
    }

    /**
     * Strips the Quill editor's HTML output down to the formatting tags its
     * toolbar can actually produce, so a tampered request can't smuggle
     * <script>/event-handler payloads into content that later renders
     * unescaped (as rich text) for every other staff member viewing it.
     *
     * strip_tags() alone only removes disallowed tag *names* — it leaves
     * attributes on whatever tags survive, so `<p onclick="...">` would pass
     * through untouched. None of the allowed tags need any attributes, so
     * the second pass strips all of them regardless of tag.
     */
    private function sanitizeDeskripsi(string $html): string
    {
        $html = strip_tags($html, '<p><br><strong><em><u><s><ol><ul><li><blockquote>');

        return preg_replace('/<(\/?\w+)[^>]*>/', '<$1>', $html);
    }

    /** Validates and moves an uploaded file into writable/uploads/disposisi. Returns the stored filename, or null. */
    private function moveUpload(string $field): ?string
    {
        /** @var UploadedFile|null $file */
        $file = $this->request->getFile($field);

        if ($file === null || !$file->isValid() || $file->hasMoved()) {
            return null;
        }

        $newName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/disposisi', $newName);

        return $newName;
    }
}
