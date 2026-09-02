<?php
/**
 * Digitized "Formulir Survey Calon Mustahik" (bahan/B2 Sragen.xlsx) — a
 * 32-question weighted eligibility assessment. Shared between
 * pengajuan/formulir.php (public) and ajuan/create.php (internal Tambah
 * Ajuan) via $this->include() — no per-request data to inject, so the
 * include-vs-view data-passing quirk doesn't apply here (both are blank
 * "create" forms, never pre-filled).
 *
 * Field names are prefixed b2_ and match tr_form_b2's columns 1:1, so
 * controllers can read $this->request->getPost('b2_<column>') directly.
 */
?>
<div class="card mb-4" id="blokFormB2">
  <div class="card-header">
    <h5 class="mb-0">Form B2 — Assessment Kelayakan Mustahik</h5>
    <small class="text-body-secondary">
      Jawab seluruh pertanyaan berikut sesuai kondisi calon mustahik saat ini. Jawaban ini digunakan untuk menilai
      tingkat kelayakan penerima bantuan.
    </small>
  </div>
  <div class="card-body">
    <h6 class="text-uppercase text-body-secondary small mb-3">Kondisi Ekonomi</h6>
    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">1. Jumlah Tanggungan Keluarga</label>
        <select name="b2_q1_tanggungan_keluarga" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">&gt; 7 orang</option>
          <option value="4">5 - 6 orang</option>
          <option value="3">3 - 4 orang</option>
          <option value="2">1 - 2 orang</option>
          <option value="1">Tidak ada</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">2. Jumlah Anak yang Masih Sekolah</label>
        <select name="b2_q2_anak_sekolah" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">7 anak</option>
          <option value="4">5 - 6 anak</option>
          <option value="3">3 - 4 anak</option>
          <option value="2">1 - 2 anak</option>
          <option value="1">Tidak ada</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">3. Jumlah Anak yang Putus Sekolah</label>
        <select name="b2_q3_anak_putus_sekolah" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">Ada</option>
          <option value="1">Tidak ada</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">4. Jumlah Pengeluaran Bulanan</label>
        <select name="b2_q4_pengeluaran_bulanan" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">&gt; Rp 3 juta</option>
          <option value="4">Rp 2 - 3 juta</option>
          <option value="3">Rp 1 - 2 juta</option>
          <option value="2">Rp 500rb - 1 juta</option>
          <option value="1">Rp 250rb - 500rb</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">5. Biaya Obat Rutin Anggota Keluarga yang Sakit</label>
        <select name="b2_q5_obat_rutin" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">&gt; Rp 1 juta</option>
          <option value="4">Rp 500rb - 1 juta</option>
          <option value="3">Rp 300rb - 500rb</option>
          <option value="2">&lt; Rp 200rb</option>
          <option value="1">Tidak ada</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">6. Biaya Pendidikan yang Ditanggung</label>
        <select name="b2_q6_biaya_pendidikan" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">&gt; Rp 2 juta</option>
          <option value="4">Rp 1,5 - 2 juta</option>
          <option value="3">Rp 1 - 1,5 juta</option>
          <option value="2">Rp 500rb - 1 juta</option>
          <option value="1">Rp 250rb - 500rb</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">7. Hutang Berjalan</label>
        <select name="b2_q7_hutang_berjalan" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">Memiliki hutang</option>
          <option value="1">Tidak memiliki hutang</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">8. Keperluan Hutang</label>
        <select name="b2_q8_keperluan_hutang" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">Kebutuhan hidup</option>
          <option value="4">Biaya kesehatan</option>
          <option value="3">Biaya pendidikan</option>
          <option value="2">Kebutuhan sosial</option>
          <option value="1">Kebutuhan sekunder</option>
        </select>
      </div>
    </div>

    <h6 class="text-uppercase text-body-secondary small mb-3 mt-2">Kondisi Keluarga</h6>
    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">9. Pekerjaan Kepala Keluarga</label>
        <select name="b2_q9_pekerjaan_kepala_keluarga" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">Menganggur</option>
          <option value="4">Serabutan</option>
          <option value="3">Karyawan</option>
          <option value="2">Dagang</option>
          <option value="1">PNS</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">10. Merokok</label>
        <select name="b2_q10_merokok" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">Tidak merokok</option>
          <option value="1">Merokok</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">11. Pekerjaan Suami/Istri</label>
        <select name="b2_q11_pekerjaan_pasangan" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">Menganggur</option>
          <option value="4">Serabutan</option>
          <option value="3">Karyawan</option>
          <option value="2">Dagang</option>
          <option value="1">PNS</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">12. Usia Mustahik</label>
        <select name="b2_q12_usia_mustahik" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">&gt; 50 tahun</option>
          <option value="4">40 - 49 tahun</option>
          <option value="3">30 - 39 tahun</option>
          <option value="2">20 - 29 tahun</option>
          <option value="1">5 - 19 tahun</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">13. Kondisi Kesehatan Kepala Keluarga</label>
        <select name="b2_q13_kondisi_kepala_keluarga" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">Sakit menahun</option>
          <option value="4">Sakit-sakitan</option>
          <option value="3">Manula</option>
          <option value="2">Sehat &amp; tidak bekerja</option>
          <option value="1">Sehat &amp; bekerja</option>
        </select>
      </div>
    </div>

    <h6 class="text-uppercase text-body-secondary small mb-3 mt-2">Kondisi Tempat Tinggal</h6>
    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">14. Kepemilikan Rumah</label>
        <select name="b2_q14_kepemilikan_rumah" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">Menumpang</option>
          <option value="4">Kontrak</option>
          <option value="3">Rumah keluarga</option>
          <option value="1">Milik sendiri</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">15. Luas Rumah</label>
        <select name="b2_q15_luas_rumah" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">Kecil (&lt; 3x7 m)</option>
          <option value="4">3x7 m</option>
          <option value="3">6x6 m</option>
          <option value="1">Luas (&gt; 6x6 m)</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">16. Dinding Rumah</label>
        <select name="b2_q16_dinding_rumah" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">Bambu</option>
          <option value="4">Seng</option>
          <option value="3">Kalsibot</option>
          <option value="2">Semi tembok</option>
          <option value="1">Batu bata</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">17. Lantai</label>
        <select name="b2_q17_lantai" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">Tanah</option>
          <option value="4">Panggung</option>
          <option value="3">Semen</option>
          <option value="1">Keramik</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">18. Atap</label>
        <select name="b2_q18_atap" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">Rumbia</option>
          <option value="4">Seng</option>
          <option value="3">Asbes</option>
          <option value="1">Genteng</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">19. Sumber Air Minum</label>
        <select name="b2_q19_sumber_air_minum" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">Tidak ada</option>
          <option value="4">Bersama/umum</option>
          <option value="3">Sumur gali</option>
          <option value="2">PDAM</option>
          <option value="1">Sumur bor</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">20. MCK</label>
        <select name="b2_q20_mck" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">Tidak ada</option>
          <option value="4">Bersama/umum</option>
          <option value="1">Sendiri</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">21. Penerangan</label>
        <select name="b2_q21_penerangan" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">Sentir/lilin</option>
          <option value="3">Saluran (nyantol)</option>
          <option value="2">PLN</option>
          <option value="1">Genset</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">22. Daya Terpasang</label>
        <select name="b2_q22_daya_terpasang" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">Tidak ada</option>
          <option value="3">450 kwh</option>
          <option value="2">900 kwh</option>
          <option value="1">1300 kwh</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">23. Kelayakan Tidur</label>
        <select name="b2_q23_kelayakan_tidur" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">Tikar/karpet</option>
          <option value="3">Kasur kapuk</option>
          <option value="2">Kasur busa</option>
          <option value="1">Spring bed</option>
        </select>
      </div>
    </div>

    <h6 class="text-uppercase text-body-secondary small mb-3 mt-2">Makanan Sehari-hari</h6>
    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">24. Jumlah Makan Per Hari</label>
        <select name="b2_q24_makan_perhari" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">1 kali</option>
          <option value="3">2 kali</option>
          <option value="1">3 kali</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">25. Konsumsi Ayam</label>
        <select name="b2_q25_konsumsi_ayam" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">Tidak pernah</option>
          <option value="4">1 kali/pekan</option>
          <option value="2">2 kali/pekan</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">26. Konsumsi Daging</label>
        <select name="b2_q26_konsumsi_daging" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">Tidak pernah</option>
          <option value="4">1 kali/pekan</option>
          <option value="1">2 kali/pekan</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">27. Konsumsi Susu</label>
        <select name="b2_q27_konsumsi_susu" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">Tidak pernah</option>
          <option value="4">1 kali/pekan</option>
          <option value="2">2 kali/pekan</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">28. Belanja Harian</label>
        <select name="b2_q28_belanja_harian" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">Rp 1rb - 15rb</option>
          <option value="4">Rp 15rb - 25rb</option>
          <option value="3">Rp 25rb - 50rb</option>
          <option value="2">Rp 50rb - 100rb</option>
          <option value="1">&gt; Rp 100rb</option>
        </select>
      </div>
    </div>

    <h6 class="text-uppercase text-body-secondary small mb-3 mt-2">Kepemilikan Aset</h6>
    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">29. Aset Tidak Bergerak (Sawah/Pekarangan)</label>
        <select name="b2_q29_aset_tidak_bergerak" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">Tidak punya</option>
          <option value="4">&le; 500 m&sup2;</option>
          <option value="2">500 - 750 m&sup2;</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">30. Barang Berharga/Benda Antik</label>
        <select name="b2_q30_barang_berharga" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">Tidak punya</option>
          <option value="4">&lt; Rp 500rb</option>
          <option value="2">Rp 500rb - 1,5 juta</option>
          <option value="1">&gt; Rp 1,5 juta</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">31. Aset Bergerak</label>
        <select name="b2_q31_aset_bergerak" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">Tidak punya</option>
          <option value="4">Sepeda</option>
          <option value="2">Motor</option>
          <option value="1">Mobil</option>
        </select>
      </div>
    </div>

    <h6 class="text-uppercase text-body-secondary small mb-3 mt-2">Barang Elektronik yang Dimiliki</h6>
    <div class="table-responsive mb-3">
      <table class="table table-bordered table-sm align-middle">
        <thead>
          <tr>
            <th>Nama Barang</th>
            <th style="width: 110px;">Jumlah</th>
            <th style="width: 200px;">Status Kepemilikan</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $barangElektronik = [
            'tv'         => 'Televisi',
            'hp'         => 'HP',
            'kulkas'     => 'Kulkas',
            'magic_com'  => 'Magic Com',
            'mesin_cuci' => 'Mesin Cuci',
            'setrika'    => 'Setrika Listrik',
            'dispenser'  => 'Dispenser',
          ];
          ?>
          <?php foreach ($barangElektronik as $key => $label): ?>
            <tr>
              <td><?= esc($label) ?></td>
              <td><input type="number" name="b2_elektronik_<?= $key ?>_jumlah" class="form-control form-control-sm" min="0" value="0" /></td>
              <td>
                <select name="b2_elektronik_<?= $key ?>_status" class="form-select form-select-sm">
                  <option value="">-- Tidak punya --</option>
                  <option value="Milik Sendiri">Milik Sendiri</option>
                  <option value="Pemberian">Pemberian</option>
                  <option value="Pinjam">Pinjam</option>
                </select>
              </td>
            </tr>
          <?php endforeach; ?>
          <tr>
            <td><input type="text" name="b2_elektronik_lainnya_nama" class="form-control form-control-sm" placeholder="Lainnya..." /></td>
            <td><input type="number" name="b2_elektronik_lainnya_jumlah" class="form-control form-control-sm" min="0" value="0" /></td>
            <td>
              <select name="b2_elektronik_lainnya_status" class="form-select form-select-sm">
                <option value="">-- Tidak punya --</option>
                <option value="Milik Sendiri">Milik Sendiri</option>
                <option value="Pemberian">Pemberian</option>
                <option value="Pinjam">Pinjam</option>
              </select>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <h6 class="text-uppercase text-body-secondary small mb-3 mt-2">Bantuan dari Lembaga Lain</h6>
    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">32. Sedang Menerima Bantuan Lain</label>
        <select name="b2_q32_bantuan_lembaga_lain" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="5">Tidak menerima bantuan apapun</option>
          <option value="5">BPJS</option>
          <option value="4">KIS/KIP</option>
          <option value="1">SARASWATI</option>
          <option value="5">PKH</option>
          <option value="4">BPNT/RASKIN</option>
        </select>
        <small class="text-body-secondary">Jika menerima lebih dari satu, pilih yang paling utama.</small>
      </div>
    </div>

    <h6 class="text-uppercase text-body-secondary small mb-3 mt-2">Lainnya</h6>
    <div class="row">
      <div class="col-12 mb-3">
        <label class="form-label">Catatan Tambahan untuk Mustahik</label>
        <textarea name="b2_catatan_tambahan" class="form-control" rows="2"></textarea>
      </div>
      <div class="col-12">
        <label class="form-label d-block">Bersedia data ini dipublikasikan?</label>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="b2_bersedia_dipublikasikan" id="b2PublikasiYa" value="1" required />
          <label class="form-check-label" for="b2PublikasiYa">Ya</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="b2_bersedia_dipublikasikan" id="b2PublikasiTidak" value="0" required />
          <label class="form-check-label" for="b2PublikasiTidak">Tidak</label>
        </div>
      </div>
    </div>
  </div>
</div>
