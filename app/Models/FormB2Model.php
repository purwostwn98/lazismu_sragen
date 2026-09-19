<?php

namespace App\Models;

use CodeIgniter\Model;

class FormB2Model extends Model
{
    protected $table      = 'tr_form_b2';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_ajuan',
        'elektronik_tv_jumlah', 'elektronik_tv_status',
        'elektronik_hp_jumlah', 'elektronik_hp_status',
        'elektronik_kulkas_jumlah', 'elektronik_kulkas_status',
        'elektronik_magic_com_jumlah', 'elektronik_magic_com_status',
        'elektronik_mesin_cuci_jumlah', 'elektronik_mesin_cuci_status',
        'elektronik_setrika_jumlah', 'elektronik_setrika_status',
        'elektronik_dispenser_jumlah', 'elektronik_dispenser_status',
        'elektronik_lainnya_nama', 'elektronik_lainnya_jumlah', 'elektronik_lainnya_status',
        'catatan_tambahan', 'bersedia_dipublikasikan',
        'total_skor', 'kategori_kelayakan',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /** The 32 scored question keys, in the same order as the paper form (bahan/B2 Sragen.xlsx). */
    public const PERTANYAAN_SKOR = [
        'q1_tanggungan_keluarga', 'q2_anak_sekolah', 'q3_anak_putus_sekolah', 'q4_pengeluaran_bulanan',
        'q5_obat_rutin', 'q6_biaya_pendidikan', 'q7_hutang_berjalan', 'q8_keperluan_hutang',
        'q9_pekerjaan_kepala_keluarga', 'q10_merokok', 'q11_pekerjaan_pasangan', 'q12_usia_mustahik',
        'q13_kondisi_kepala_keluarga', 'q14_kepemilikan_rumah', 'q15_luas_rumah', 'q16_dinding_rumah',
        'q17_lantai', 'q18_atap', 'q19_sumber_air_minum', 'q20_mck', 'q21_penerangan', 'q22_daya_terpasang',
        'q23_kelayakan_tidur', 'q24_makan_perhari', 'q25_konsumsi_ayam', 'q26_konsumsi_daging',
        'q27_konsumsi_susu', 'q28_belanja_harian', 'q29_aset_tidak_bergerak', 'q30_barang_berharga',
        'q31_aset_bergerak', 'q32_bantuan_lembaga_lain',
    ];

    /**
     * Score -> answer text, frozen as of the options in
     * disposisi/_form_b2.php today - used ONLY as a display fallback for
     * ajuan submitted before the "<key>_opsi" columns existed (which have
     * a score saved but no stored answer text). New submissions always use
     * their own stored _opsi text instead of this table, so it does not
     * need to stay in sync with the form going forward; it's a one-time
     * historical reconstruction, not a live source of truth.
     *
     * q32 is deliberately excluded: several of its answers have always
     * shared the same score (even before _opsi existed), so a historical
     * score alone can't say which one was picked - those rows keep
     * showing "Skor X" since there's no way to recover the real answer.
     */
    public const SKOR_KE_LABEL_HISTORIS = [
        'q1_tanggungan_keluarga'       => [5 => '> 7 orang', 4 => '5 - 6 orang', 3 => '3 - 4 orang', 2 => '1 - 2 orang', 1 => 'Tidak ada'],
        'q2_anak_sekolah'              => [5 => '7 anak', 4 => '5 - 6 anak', 3 => '3 - 4 anak', 2 => '1 - 2 anak', 1 => 'Tidak ada'],
        'q3_anak_putus_sekolah'        => [5 => 'Ada', 1 => 'Tidak ada'],
        'q4_pengeluaran_bulanan'       => [5 => '> Rp 3 juta', 4 => 'Rp 2 - 3 juta', 3 => 'Rp 1 - 2 juta', 2 => 'Rp 500rb - 1 juta', 1 => 'Rp 250rb - 500rb'],
        'q5_obat_rutin'                => [5 => '> Rp 1 juta', 4 => 'Rp 500rb - 1 juta', 3 => 'Rp 300rb - 500rb', 2 => '< Rp 200rb', 1 => 'Tidak ada'],
        'q6_biaya_pendidikan'          => [5 => '> Rp 2 juta', 4 => 'Rp 1,5 - 2 juta', 3 => 'Rp 1 - 1,5 juta', 2 => 'Rp 500rb - 1 juta', 1 => 'Rp 250rb - 500rb'],
        'q7_hutang_berjalan'           => [5 => 'Memiliki hutang', 1 => 'Tidak memiliki hutang'],
        'q8_keperluan_hutang'          => [5 => 'Kebutuhan hidup', 4 => 'Biaya kesehatan', 3 => 'Biaya pendidikan', 2 => 'Kebutuhan sosial', 1 => 'Kebutuhan sekunder', 0 => 'Tidak memiliki hutang'],
        'q9_pekerjaan_kepala_keluarga' => [5 => 'Menganggur', 4 => 'Serabutan', 3 => 'Karyawan', 2 => 'Dagang', 1 => 'PNS'],
        'q10_merokok'                  => [5 => 'Tidak merokok', 1 => 'Merokok'],
        'q11_pekerjaan_pasangan'       => [5 => 'Menganggur', 4 => 'Serabutan', 3 => 'Karyawan', 2 => 'Dagang', 1 => 'PNS'],
        'q12_usia_mustahik'            => [5 => '> 50 tahun', 4 => '40 - 49 tahun', 3 => '30 - 39 tahun', 2 => '20 - 29 tahun', 1 => '5 - 19 tahun'],
        'q13_kondisi_kepala_keluarga'  => [5 => 'Sakit menahun', 4 => 'Sakit-sakitan', 3 => 'Manula', 2 => 'Sehat & tidak bekerja', 1 => 'Sehat & bekerja'],
        'q14_kepemilikan_rumah'        => [5 => 'Menumpang', 4 => 'Kontrak', 3 => 'Rumah keluarga', 1 => 'Milik sendiri'],
        'q15_luas_rumah'               => [5 => 'Kecil (< 3x7 m)', 4 => '3x7 m', 3 => '6x6 m', 1 => 'Luas (> 6x6 m)'],
        'q16_dinding_rumah'            => [5 => 'Bambu', 4 => 'Seng', 3 => 'Kalsibot', 2 => 'Semi tembok', 1 => 'Batu bata'],
        'q17_lantai'                   => [5 => 'Tanah', 4 => 'Panggung', 3 => 'Semen', 1 => 'Keramik'],
        'q18_atap'                     => [5 => 'Rumbia', 4 => 'Seng', 3 => 'Asbes', 1 => 'Genteng'],
        'q19_sumber_air_minum'         => [5 => 'Tidak ada', 4 => 'Bersama/umum', 3 => 'Sumur gali', 2 => 'PDAM', 1 => 'Sumur bor'],
        'q20_mck'                      => [5 => 'Tidak ada', 4 => 'Bersama/umum', 1 => 'Sendiri'],
        'q21_penerangan'               => [5 => 'Sentir/lilin', 3 => 'Saluran (nyantol)', 2 => 'PLN', 1 => 'Genset'],
        'q22_daya_terpasang'           => [5 => 'Tidak ada', 3 => '450 kwh', 2 => '900 kwh', 1 => '1300 kwh'],
        'q23_kelayakan_tidur'          => [5 => 'Tikar/karpet', 3 => 'Kasur kapuk', 2 => 'Kasur busa', 1 => 'Spring bed'],
        'q24_makan_perhari'            => [5 => '1 kali', 3 => '2 kali', 1 => '3 kali'],
        'q25_konsumsi_ayam'            => [5 => 'Tidak pernah', 4 => '1 kali/pekan', 2 => '2 kali/pekan'],
        'q26_konsumsi_daging'          => [5 => 'Tidak pernah', 4 => '1 kali/pekan', 1 => '2 kali/pekan'],
        'q27_konsumsi_susu'            => [5 => 'Tidak pernah', 4 => '1 kali/pekan', 2 => '2 kali/pekan'],
        'q28_belanja_harian'           => [5 => 'Rp 1rb - 15rb', 4 => 'Rp 15rb - 25rb', 3 => 'Rp 25rb - 50rb', 2 => 'Rp 50rb - 100rb', 1 => '> Rp 100rb'],
        'q29_aset_tidak_bergerak'      => [5 => 'Tidak punya', 4 => '<= 500 m²', 2 => '500 - 750 m²'],
        'q30_barang_berharga'          => [5 => 'Tidak punya', 4 => '< Rp 500rb', 2 => 'Rp 500rb - 1,5 juta', 1 => '> Rp 1,5 juta'],
        'q31_aset_bergerak'            => [5 => 'Tidak punya', 4 => 'Sepeda', 2 => 'Motor', 1 => 'Mobil'],
    ];

    /** Question key => the label each question carries on the assessment form (see disposisi/_form_b2.php). */
    public const LABEL_PERTANYAAN = [
        'q1_tanggungan_keluarga' => 'Jumlah Tanggungan Keluarga',
        'q2_anak_sekolah' => 'Jumlah Anak yang Masih Sekolah',
        'q3_anak_putus_sekolah' => 'Jumlah Anak yang Putus Sekolah',
        'q4_pengeluaran_bulanan' => 'Jumlah Pengeluaran Bulanan',
        'q5_obat_rutin' => 'Biaya Obat Rutin Anggota Keluarga yang Sakit',
        'q6_biaya_pendidikan' => 'Biaya Pendidikan yang Ditanggung',
        'q7_hutang_berjalan' => 'Hutang Berjalan',
        'q8_keperluan_hutang' => 'Keperluan Hutang',
        'q9_pekerjaan_kepala_keluarga' => 'Pekerjaan Kepala Keluarga',
        'q10_merokok' => 'Merokok',
        'q11_pekerjaan_pasangan' => 'Pekerjaan Suami/Istri',
        'q12_usia_mustahik' => 'Usia Mustahik',
        'q13_kondisi_kepala_keluarga' => 'Kondisi Kesehatan Kepala Keluarga',
        'q14_kepemilikan_rumah' => 'Kepemilikan Rumah',
        'q15_luas_rumah' => 'Luas Rumah',
        'q16_dinding_rumah' => 'Dinding Rumah',
        'q17_lantai' => 'Lantai',
        'q18_atap' => 'Atap',
        'q19_sumber_air_minum' => 'Sumber Air Minum',
        'q20_mck' => 'MCK',
        'q21_penerangan' => 'Penerangan',
        'q22_daya_terpasang' => 'Daya Terpasang',
        'q23_kelayakan_tidur' => 'Kelayakan Tidur',
        'q24_makan_perhari' => 'Jumlah Makan Per Hari',
        'q25_konsumsi_ayam' => 'Konsumsi Ayam',
        'q26_konsumsi_daging' => 'Konsumsi Daging',
        'q27_konsumsi_susu' => 'Konsumsi Susu',
        'q28_belanja_harian' => 'Belanja Harian',
        'q29_aset_tidak_bergerak' => 'Aset Tidak Bergerak (Sawah/Pekarangan)',
        'q30_barang_berharga' => 'Barang Berharga/Benda Antik',
        'q31_aset_bergerak' => 'Aset Bergerak',
        'q32_bantuan_lembaga_lain' => 'Sedang Menerima Bantuan Lain',
    ];

    public function __construct()
    {
        parent::__construct();

        // Every PERTANYAAN_SKOR question (the score itself) plus its
        // companion "<key>_opsi" column (added by
        // 2026-09-13-000001_AddOpsiToFormB2, storing the exact answer label
        // picked - see FormB2Reader, which posts "score|label" per
        // question). Declared here instead of spelled out in $allowedFields
        // above so the two lists can't drift apart as questions are added/
        // renamed.
        foreach (self::PERTANYAAN_SKOR as $key) {
            $this->allowedFields[] = $key;
            $this->allowedFields[] = $key . '_opsi';
        }
    }

    /**
     * Sums the 32 question scores and classifies the result into the same
     * 3 bands printed on the paper form. The paper form's bands only cover
     * 41-155 (min possible is 32 x 1 = 32), so the bottom band is widened
     * down to catch any lower score rather than leaving it unclassified.
     */
    public function totalDanKategori(array $jawaban): array
    {
        $total = 0;
        foreach (self::PERTANYAAN_SKOR as $key) {
            $total += (int) ($jawaban[$key] ?? 0);
        }

        if ($total >= 92) {
            $kategori = 'Sangat Perlu Dibantu';
        } elseif ($total >= 65) {
            $kategori = 'Layak Dibantu';
        } else {
            $kategori = 'Belum Layak Dibantu';
        }

        return ['total_skor' => $total, 'kategori_kelayakan' => $kategori];
    }

    /**
     * The answer text for one question of a saved row: the stored "<key>_opsi"
     * label, else the frozen historical score->label lookup (rows saved
     * before _opsi existed), else the bare score as a last resort.
     */
    public function jawabanLabel(array $row, string $key): string
    {
        $skor = (int) ($row[$key] ?? 0);

        return $row[$key . '_opsi'] ?? self::SKOR_KE_LABEL_HISTORIS[$key][$skor] ?? ('Skor ' . $skor);
    }

    /**
     * Maps a saved tr_form_b2 row back to disposisi/_form_b2.php's b2_*
     * field names and values, for pre-filling the form when the Surveyor
     * edits an existing assessment. Question selects use the same
     * "score|label" value their <option>s carry; the label comes from the
     * stored _opsi text, falling back to SKOR_KE_LABEL_HISTORIS for older
     * rows. A question with neither (e.g. legacy q32, whose score is
     * ambiguous) is left out so the field stays blank and must be re-picked.
     */
    public function nilaiForm(array $row): array
    {
        $nilai = [];

        foreach (self::PERTANYAAN_SKOR as $key) {
            $skor  = (int) ($row[$key] ?? 0);
            $label = $row[$key . '_opsi'] ?? self::SKOR_KE_LABEL_HISTORIS[$key][$skor] ?? null;

            if ($label !== null) {
                $nilai['b2_' . $key] = $skor . '|' . $label;
            }
        }

        foreach ($row as $kolom => $isi) {
            if (str_starts_with($kolom, 'elektronik_') || in_array($kolom, ['catatan_tambahan', 'bersedia_dipublikasikan'], true)) {
                $nilai['b2_' . $kolom] = $isi === null ? '' : (string) $isi;
            }
        }

        return $nilai;
    }

    /** Insert a new B2 assessment for this ajuan, or refresh the existing one (one row per nomor_ajuan). */
    public function upsert(string $nomorAjuan, array $data): void
    {
        $existing = $this->where('nomor_ajuan', $nomorAjuan)->first();

        if ($existing) {
            $this->update($existing['id'], $data);
        } else {
            $this->insert(array_merge(['nomor_ajuan' => $nomorAjuan], $data));
        }
    }
}
