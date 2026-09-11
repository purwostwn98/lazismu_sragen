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
     * pengajuan/_form_b2.php today - used ONLY as a display fallback for
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
