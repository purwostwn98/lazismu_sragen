<?php

namespace App\Controllers;

class AnalitikController extends BaseController
{
    public function index()
    {
        $db       = \Config\Database::connect();
        $tahunNow = (int) date('Y');

        // =====================================================================
        // Filter Tahun
        // =====================================================================
        $minTahunRow = $db->query('SELECT MIN(YEAR(tgl_diajukan)) AS thn FROM tr_ajuan')->getRow();
        $tahunAwal   = $minTahunRow && $minTahunRow->thn ? (int) $minTahunRow->thn : $tahunNow;

        $tahunGet    = $this->request->getGet('tahun');
        $tahunFilter = (is_numeric($tahunGet) && (int) $tahunGet >= $tahunAwal && (int) $tahunGet <= $tahunNow)
            ? (int) $tahunGet
            : 'all';

        $tahunList = range($tahunAwal, $tahunNow);

        // Clause helpers (values are validated integers, safe to inline).
        $onAjuan = $tahunFilter !== 'all' ? "AND YEAR(ta.tgl_diajukan) = $tahunFilter" : '';
        $onBa    = $tahunFilter !== 'all' ? "AND YEAR(ab.tanggal_penyerahan) = $tahunFilter" : '';
        $andAjuan = $tahunFilter !== 'all' ? "AND YEAR(ta.tgl_diajukan) = $tahunFilter" : '';
        $andDana  = $tahunFilter !== 'all' ? "AND YEAR(ab.tanggal_penyerahan) = $tahunFilter" : '';
        $whereDanaTotal = $tahunFilter !== 'all' ? "WHERE YEAR(tanggal_penyerahan) = $tahunFilter" : '';

        // =====================================================================
        // SECTION 1 — Ajuan Terbanyak: per Pilar & per Daerah
        // =====================================================================

        $ajuanPerPilar = $db->query("
            SELECT dp.id_pilar, dp.nama_pilar,
                   COUNT(ta.id_ajuan) AS jumlah
            FROM dt_pilar dp
            LEFT JOIN ad_kategori_program kp ON kp.id_pilar = dp.id_pilar
            LEFT JOIN tr_ajuan ta
                   ON ta.id_kategori_program = kp.id_kategori_program $onAjuan
            GROUP BY dp.id_pilar, dp.nama_pilar
            ORDER BY jumlah DESC
        ")->getResultArray();

        // Uncapped, for the "Kab/Kota Terlayani" KPI tile — independent of the
        // Top 20 Kecamatan chart below so the LIMIT there doesn't cap this count.
        $kabupatenTerlayaniRow = $db->query("
            SELECT COUNT(DISTINCT tp.id_kabupaten) AS jumlah
            FROM tr_ajuan ta
            JOIN tr_pemohon tp ON ta.nik = tp.nik
            WHERE 1=1 $andAjuan
        ")->getRow();
        $kabupatenTerlayani = $kabupatenTerlayaniRow ? (int) $kabupatenTerlayaniRow->jumlah : 0;

        $ajuanPerKecamatan = $db->query("
            SELECT dc.nama_kecamatan, COUNT(ta.id_ajuan) AS jumlah
            FROM tr_ajuan ta
            JOIN tr_pemohon tp ON ta.nik = tp.nik
            JOIN dt_kecamatan dc ON tp.id_kecamatan = dc.id_kecamatan
            WHERE 1=1 $andAjuan
            GROUP BY dc.id_kecamatan, dc.nama_kecamatan
            ORDER BY jumlah DESC
            LIMIT 20
        ")->getResultArray();

        // =====================================================================
        // SECTION 2 — Dana Tersalurkan: per Pilar & per Daerah
        // =====================================================================

        // Pilar untuk dana tersalurkan diambil dari Form B3 (id_kategori_program
        // yang admin tetapkan saat pencairan), bukan dari kategori_program pada
        // ajuan awal — keduanya bisa berbeda jika B3 mengubah klasifikasinya.
        $danaPerPilar = $db->query("
            SELECT dp.nama_pilar,
                   COALESCE(SUM(ab.nilai_penyerahan), 0) AS total_tersalurkan,
                   COUNT(DISTINCT ab.id_berita_acara)    AS jumlah_penyerahan
            FROM dt_pilar dp
            LEFT JOIN ad_kategori_program kp ON kp.id_pilar = dp.id_pilar
            LEFT JOIN tr_form_b3 b3 ON b3.id_kategori_program = kp.id_kategori_program
            LEFT JOIN ad_berita_acara ab
                   ON ab.nomor_ajuan = b3.nomor_ajuan $onBa
            GROUP BY dp.id_pilar, dp.nama_pilar
            ORDER BY total_tersalurkan DESC
        ")->getResultArray();

        $totalTersalurkanRow = $db->query("
            SELECT COALESCE(SUM(nilai_penyerahan), 0) AS total
            FROM ad_berita_acara
            $whereDanaTotal
        ")->getRow();
        $grandTotalDana = $totalTersalurkanRow ? (float) $totalTersalurkanRow->total : 0;

        $danaPerKecamatan = $db->query("
            SELECT dc.nama_kecamatan,
                   COALESCE(SUM(ab.nilai_penyerahan), 0) AS total_tersalurkan
            FROM ad_berita_acara ab
            JOIN tr_ajuan ta ON ab.nomor_ajuan = ta.nomor_ajuan
            JOIN tr_pemohon tp ON ta.nik = tp.nik
            JOIN dt_kecamatan dc ON tp.id_kecamatan = dc.id_kecamatan
            WHERE 1=1 $andDana
            GROUP BY dc.id_kecamatan, dc.nama_kecamatan
            ORDER BY total_tersalurkan DESC
            LIMIT 20
        ")->getResultArray();

        // =====================================================================
        // SECTION 3 — Historis (selalu sejak tahun data paling awal, tidak
        //             difilter). Statistik & rekomendasi mengikuti filter tahun.
        // =====================================================================

        $trenAjuanRaw = $db->query("
            SELECT YEAR(ta.tgl_diajukan) AS tahun,
                   dp.nama_pilar,
                   COUNT(ta.id_ajuan)   AS jumlah_ajuan
            FROM tr_ajuan ta
            JOIN ad_kategori_program kp ON ta.id_kategori_program = kp.id_kategori_program
            JOIN dt_pilar dp ON kp.id_pilar = dp.id_pilar
            WHERE YEAR(ta.tgl_diajukan) >= $tahunAwal
            GROUP BY tahun, dp.id_pilar, dp.nama_pilar
            ORDER BY tahun, dp.nama_pilar
        ")->getResultArray();

        $trenDanaRaw = $db->query("
            SELECT YEAR(ab.tanggal_penyerahan) AS tahun,
                   dp.nama_pilar,
                   COALESCE(SUM(ab.nilai_penyerahan), 0) AS total_tersalurkan
            FROM ad_berita_acara ab
            JOIN tr_form_b3 b3 ON ab.nomor_ajuan = b3.nomor_ajuan
            JOIN ad_kategori_program kp ON b3.id_kategori_program = kp.id_kategori_program
            JOIN dt_pilar dp ON kp.id_pilar = dp.id_pilar
            WHERE YEAR(ab.tanggal_penyerahan) >= $tahunAwal
              AND ab.tanggal_penyerahan IS NOT NULL
            GROUP BY tahun, dp.id_pilar, dp.nama_pilar
            ORDER BY tahun, dp.nama_pilar
        ")->getResultArray();

        // Pilar untuk seluruh tabel ini mengikuti Form B3 saat sudah diisi
        // (id_kategori_program yang admin tetapkan saat pencairan), dan baru
        // jatuh kembali ke kategori_program pada ajuan awal jika B3 belum ada
        // — supaya ajuan yang belum sampai tahap B3 tetap terhitung.
        $statistikPilar = $db->query("
            SELECT dp.nama_pilar,
                   COUNT(ta.id_ajuan)                                                AS total_ajuan,
                   SUM(CASE WHEN ta.status_ajuan IN (7,8,9) THEN 1 ELSE 0 END)      AS total_disetujui,
                   SUM(CASE WHEN ta.status_ajuan = 6        THEN 1 ELSE 0 END)      AS total_ditolak,
                   SUM(CASE WHEN ta.status_ajuan IN (0,1,2,3,4,5) THEN 1 ELSE 0 END) AS total_pending,
                   COALESCE(AVG(CASE WHEN ta.status_ajuan IN (7,8,9)
                       THEN ta.nilai_disetujui END), 0)                             AS avg_nilai_disetujui,
                   COALESCE(SUM(CASE WHEN ta.status_ajuan IN (7,8,9)
                       THEN ta.nilai_disetujui ELSE 0 END), 0)                      AS total_nilai_disetujui,
                   COALESCE(MAX(CASE WHEN ta.status_ajuan IN (7,8,9)
                       THEN ta.nilai_disetujui END), 0)                             AS maks_nilai,
                   COALESCE(MIN(CASE WHEN ta.status_ajuan IN (7,8,9)
                       AND ta.nilai_disetujui > 0 THEN ta.nilai_disetujui END), 0)  AS min_nilai,
                   COALESCE(ts.total_tersalurkan, 0)                                AS total_tersalurkan
            FROM dt_pilar dp
            LEFT JOIN ad_kategori_program kp ON kp.id_pilar = dp.id_pilar
            LEFT JOIN (
                SELECT t.id_ajuan, t.status_ajuan, t.nilai_disetujui, t.tgl_diajukan,
                       COALESCE(b3.id_kategori_program, t.id_kategori_program) AS id_kategori_program
                FROM tr_ajuan t
                LEFT JOIN tr_form_b3 b3 ON b3.nomor_ajuan = t.nomor_ajuan
            ) ta ON ta.id_kategori_program = kp.id_kategori_program $onAjuan
            LEFT JOIN (
                SELECT kp2.id_pilar,
                       COALESCE(SUM(ab.nilai_penyerahan), 0) AS total_tersalurkan
                FROM ad_berita_acara ab
                JOIN tr_form_b3 b3_2 ON b3_2.nomor_ajuan = ab.nomor_ajuan
                JOIN ad_kategori_program kp2 ON b3_2.id_kategori_program = kp2.id_kategori_program
                WHERE 1=1 $andDana
                GROUP BY kp2.id_pilar
            ) ts ON ts.id_pilar = dp.id_pilar
            GROUP BY dp.id_pilar, dp.nama_pilar
            ORDER BY total_ajuan DESC
        ")->getResultArray();

        // =====================================================================
        // Pre-process untuk chart
        // =====================================================================

        $tahunAjuanList = array_unique(array_column($trenAjuanRaw, 'tahun'));
        sort($tahunAjuanList);
        $pilarList = array_unique(array_column($ajuanPerPilar, 'nama_pilar'));

        $trenAjuanMatrix = [];
        foreach ($trenAjuanRaw as $row) {
            $trenAjuanMatrix[$row['nama_pilar']][$row['tahun']] = (int) $row['jumlah_ajuan'];
        }

        $tahunDanaList = array_unique(array_column($trenDanaRaw, 'tahun'));
        sort($tahunDanaList);

        $trenDanaMatrix = [];
        foreach ($trenDanaRaw as $row) {
            $trenDanaMatrix[$row['nama_pilar']][$row['tahun']] = (float) $row['total_tersalurkan'];
        }

        // Rekomendasi anggaran — growth rate selalu pakai 2 tahun penuh terakhir.
        $rekomendasiAnggaran = [];
        foreach ($statistikPilar as $sp) {
            $nama       = $sp['nama_pilar'];
            $growthRate = 0;
            $proyeksi   = 0;

            $t2 = isset($trenDanaMatrix[$nama][$tahunNow - 1]) ? (float) $trenDanaMatrix[$nama][$tahunNow - 1] : 0;
            $t3 = isset($trenDanaMatrix[$nama][$tahunNow - 2]) ? (float) $trenDanaMatrix[$nama][$tahunNow - 2] : 0;

            if ($t3 > 0 && $t2 > 0) {
                $growthRate = (($t2 - $t3) / $t3) * 100;
                $proyeksi   = $t2 * (1 + $growthRate / 100);
            } elseif ($t2 > 0) {
                $proyeksi = $t2 * 1.1;
            } else {
                $proyeksi = (float) $sp['avg_nilai_disetujui'] * max(1, (int) $sp['total_disetujui']);
            }

            $approvalRate = $sp['total_ajuan'] > 0 ? round(($sp['total_disetujui'] / $sp['total_ajuan']) * 100, 1) : 0;

            $rekomendasiAnggaran[] = [
                'nama_pilar'        => $nama,
                'total_ajuan'       => $sp['total_ajuan'],
                'approval_rate'     => $approvalRate,
                'avg_disetujui'     => round($sp['avg_nilai_disetujui']),
                'total_disetujui'   => $sp['total_nilai_disetujui'],
                'total_tersalurkan' => $sp['total_tersalurkan'],
                'growth_rate'       => round($growthRate, 1),
                'proyeksi_anggaran' => round($proyeksi),
            ];
        }

        $data = [
            'title'                => 'Dashboard Analitik',
            'activeMenu'           => 'analitik',
            'tahunFilter'          => $tahunFilter,
            'tahunList'            => $tahunList,
            'ajuanPerPilar'        => $ajuanPerPilar,
            'ajuanPerKecamatan'    => $ajuanPerKecamatan,
            'kabupatenTerlayani'   => $kabupatenTerlayani,
            'danaPerPilar'         => $danaPerPilar,
            'danaPerKecamatan'     => $danaPerKecamatan,
            'grandTotalDana'       => $grandTotalDana,
            'statistikPilar'       => $statistikPilar,
            'tahunAjuanList'       => $tahunAjuanList,
            'pilarList'            => $pilarList,
            'trenAjuanMatrix'      => $trenAjuanMatrix,
            'tahunDanaList'        => $tahunDanaList,
            'trenDanaMatrix'       => $trenDanaMatrix,
            'rekomendasiAnggaran'  => $rekomendasiAnggaran,
        ];

        return view('analitik/index', $data);
    }
}
