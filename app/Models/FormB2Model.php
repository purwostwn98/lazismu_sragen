<?php

namespace App\Models;

use CodeIgniter\Model;

class FormB2Model extends Model
{
    protected $table      = 'tr_form_b2';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_ajuan',
        'q1_tanggungan_keluarga', 'q2_anak_sekolah', 'q3_anak_putus_sekolah', 'q4_pengeluaran_bulanan',
        'q5_obat_rutin', 'q6_biaya_pendidikan', 'q7_hutang_berjalan', 'q8_keperluan_hutang',
        'q9_pekerjaan_kepala_keluarga', 'q10_merokok', 'q11_pekerjaan_pasangan', 'q12_usia_mustahik',
        'q13_kondisi_kepala_keluarga', 'q14_kepemilikan_rumah', 'q15_luas_rumah', 'q16_dinding_rumah',
        'q17_lantai', 'q18_atap', 'q19_sumber_air_minum', 'q20_mck', 'q21_penerangan', 'q22_daya_terpasang',
        'q23_kelayakan_tidur', 'q24_makan_perhari', 'q25_konsumsi_ayam', 'q26_konsumsi_daging',
        'q27_konsumsi_susu', 'q28_belanja_harian', 'q29_aset_tidak_bergerak', 'q30_barang_berharga',
        'q31_aset_bergerak', 'q32_bantuan_lembaga_lain',
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
