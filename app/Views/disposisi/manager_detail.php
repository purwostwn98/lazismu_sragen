<?= $this->extend('layouts/main') ?>

<?= $this->section('pageStyles') ?>
<link rel="stylesheet" href="<?= base_url('assets/vendor/libs/quill/typography.css') ?>" />
<link rel="stylesheet" href="<?= base_url('assets/vendor/libs/quill/editor.css') ?>" />
<style>
  .ajuan-field-label {
    display: block;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--bs-body-color-secondary, #6b7280);
    margin-bottom: 0.25rem;
  }

  .ajuan-field-value {
    font-weight: 500;
  }

  .ajuan-value-box {
    border: 1px solid var(--bs-border-color);
    border-radius: 0.5rem;
    padding: 0.9rem 1rem;
    height: 100%;
  }

  #deskripsiEditor {
    min-height: 160px;
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('pageScriptsVendor') ?>
<script src="<?= base_url('assets/vendor/libs/quill/quill.js') ?>"></script>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<?php
$ajuan          = $ajuan ?? [];
$individu       = $individu ?? null;
$lembaga        = $lembaga ?? null;
$b2             = $b2 ?? null;
$survey         = $survey ?? null;
$kadiv          = $kadiv ?? null;
$riwayatManager = $riwayatManager ?? [];
$latestManager  = $latestManager ?? ($riwayatManager[0] ?? null);

$statusColor = ajuan_status_color(isset($ajuan['status_ajuan']) ? (int) $ajuan['status_ajuan'] : null);
?>

<div class="card mb-4">
  <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div class="d-flex align-items-center gap-3">
      <div class="avatar avatar-lg flex-shrink-0">
        <span class="avatar-initial rounded bg-label-<?= $statusColor ?>">
          <i class="icon-base ti tabler-user-star icon-lg"></i>
        </span>
      </div>
      <div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <h4 class="mb-0">Tinjau Ajuan #<?= esc($ajuan['nomor_ajuan']) ?></h4>
          <span class="badge bg-label-<?= $statusColor ?>"><?= esc($ajuan['keterangan_status'] ?? '-') ?></span>
          <span class="badge bg-label-secondary"><?= esc($ajuan['jenis_ajuan']) ?></span>
          <?php if (!empty($ajuan['is_internal'])): ?>
            <span class="badge bg-label-info">Internal</span>
          <?php endif; ?>
        </div>
        <p class="text-body-secondary mb-0">
          <?= esc($ajuan['nama_pemohon'] ?? '-') ?> &middot; <?= esc($ajuan['nama_program'] ?? '-') ?>
        </p>
      </div>
    </div>
    <a href="<?= base_url('disposisi/manager') ?>" class="btn btn-label-secondary btn-sm">
      <i class="icon-base ti tabler-arrow-left me-1"></i>Kembali
    </a>
  </div>
</div>

<div class="card mb-4">
  <div class="card-header">
    <h5 class="mb-0">Informasi Ajuan</h5>
  </div>
  <div class="card-body">
    <div class="row g-3 mb-3">
      <div class="col-md-4">
        <div class="ajuan-value-box">
          <span class="ajuan-field-label">Nilai Diajukan</span>
          <span class="ajuan-field-value fs-5">Rp <?= number_format((float) $ajuan['nilai_diajukan'], 0, ',', '.') ?></span>
        </div>
      </div>
      <div class="col-md-4">
        <div class="ajuan-value-box">
          <span class="ajuan-field-label">Nilai Disetujui</span>
          <span class="ajuan-field-value fs-5">
            <?= $ajuan['nilai_disetujui'] !== null ? 'Rp ' . number_format((float) $ajuan['nilai_disetujui'], 0, ',', '.') : '-' ?>
          </span>
        </div>
      </div>
      <div class="col-md-4">
        <div class="ajuan-value-box">
          <span class="ajuan-field-label">Tanggal Diajukan</span>
          <span class="ajuan-field-value fs-5"><?= esc($ajuan['tgl_diajukan']) ?></span>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6 mb-3">
        <span class="ajuan-field-label">Pemohon</span>
        <span class="ajuan-field-value"><?= esc($ajuan['nama_pemohon'] ?? '-') ?> (<?= esc($ajuan['nik']) ?>)</span>
      </div>
      <div class="col-md-6 mb-3">
        <span class="ajuan-field-label">No. Telepon Pemohon</span>
        <span class="ajuan-field-value">
          <?php
          $teleponPemohon = $ajuan['telepon'] ?? '';
          $teleponPemohon = is_array($teleponPemohon) ? implode('', $teleponPemohon) : (string) $teleponPemohon;
          $nomorWaPemohon = !empty($teleponPemohon) ? preg_replace('/^0/', '62', preg_replace('/\D/', '', $teleponPemohon)) : '';
          ?>
          <?php if (!empty($teleponPemohon)): ?>
            <a href="https://wa.me/<?= esc($nomorWaPemohon) ?>" target="_blank" rel="noopener">
              <?= esc($teleponPemohon) ?>
            </a>
          <?php else: ?>
            -
          <?php endif; ?>
        </span>
      </div>
      <div class="col-md-6 mb-3">
        <span class="ajuan-field-label">Kegiatan</span>
        <span class="ajuan-field-value"><?= esc($ajuan['nama_program'] ?? '-') ?></span>
      </div>
      <div class="col-12 mb-3">
        <span class="ajuan-field-label">Deskripsi</span>
        <span class="ajuan-field-value"><?= nl2br(esc($ajuan['deskripsi_ajuan'])) ?></span>
      </div>
      <div class="col-12<?= !empty($ajuan['is_internal']) ? ' mb-3' : '' ?>">
        <span class="ajuan-field-label">Proposal</span>
        <?php if (!empty($ajuan['file_proposal'])): ?>
          <a href="<?= base_url('ajuan/' . $ajuan['nomor_ajuan'] . '/dokumen/proposal') ?>" target="_blank" class="btn btn-sm btn-label-secondary">
            <i class="icon-base ti tabler-file-text me-1"></i>Lihat Proposal
          </a>
        <?php else: ?>
          <span class="ajuan-field-value text-body-secondary d-block">Belum diunggah</span>
        <?php endif; ?>
      </div>
      <?php if (!empty($ajuan['is_internal'])): ?>
        <div class="col-md-6 mb-3">
          <span class="ajuan-field-label">Memo</span>
          <?php if (!empty($ajuan['file_memo'])): ?>
            <a href="<?= base_url('ajuan/' . $ajuan['nomor_ajuan'] . '/dokumen/memo') ?>" target="_blank" class="btn btn-sm btn-label-secondary">
              <i class="icon-base ti tabler-file-text me-1"></i>Lihat Memo
            </a>
          <?php else: ?>
            <span class="ajuan-field-value text-body-secondary d-block">Belum diunggah</span>
          <?php endif; ?>
        </div>
        <div class="col-md-6 mb-3">
          <span class="ajuan-field-label">Deskripsi Memo</span>
          <span class="ajuan-field-value"><?= !empty($ajuan['deskripsi_memo']) ? nl2br(esc($ajuan['deskripsi_memo'])) : '-' ?></span>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php if ($individu): ?>
  <div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h5 class="mb-0">Data Mustahik (Individu)</h5>
      <a href="<?= base_url('ajuan/' . $ajuan['nomor_ajuan'] . '/form-b1') ?>" class="btn btn-sm btn-label-secondary">
        <i class="icon-base ti tabler-download me-1"></i>Download Form B1
      </a>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6 mb-3"><span class="ajuan-field-label">Nama</span><span class="ajuan-field-value"><?= esc($individu['nama_mustahik']) ?></span></div>
        <div class="col-md-6 mb-3"><span class="ajuan-field-label">Jenis Kelamin</span><span class="ajuan-field-value"><?= esc($individu['kelamin_mustahik']) ?></span></div>
        <div class="col-md-6 mb-3"><span class="ajuan-field-label">Tempat, Tanggal Lahir</span><span class="ajuan-field-value"><?= esc($individu['tempat_lahir']) ?>, <?= esc($individu['tgl_lahir']) ?></span></div>
        <div class="col-md-6 mb-3"><span class="ajuan-field-label">Agama</span><span class="ajuan-field-value"><?= esc($individu['agama_mustahik']) ?></span></div>
        <div class="col-md-6 mb-3"><span class="ajuan-field-label">Status Pendidikan</span><span class="ajuan-field-value"><?= esc($individu['status_pendidikan']) ?></span></div>
        <div class="col-md-6 mb-3"><span class="ajuan-field-label">Status Marital</span><span class="ajuan-field-value"><?= esc($individu['status_marital']) ?></span></div>
        <div class="col-md-6 mb-3"><span class="ajuan-field-label">Jumlah Keluarga</span><span class="ajuan-field-value"><?= esc($individu['jml_keluarga']) ?></span></div>
        <div class="col-md-6 mb-3"><span class="ajuan-field-label">No. Handphone</span><span class="ajuan-field-value"><?= esc($individu['no_handphone']) ?></span></div>
        <div class="col-md-6 mb-3"><span class="ajuan-field-label">Email</span><span class="ajuan-field-value"><?= esc($individu['email']) ?></span></div>
        <div class="col-md-6 mb-3"><span class="ajuan-field-label">Alamat</span><span class="ajuan-field-value"><?= esc($individu['alamat']) ?></span></div>
        <div class="col-md-6">
          <span class="ajuan-field-label">Dokumen KTP</span>
          <?php if (!empty($individu['foto_ktp'])): ?>
            <a href="<?= base_url('ajuan/' . $ajuan['nomor_ajuan'] . '/mustahik/ktp') ?>" target="_blank" class="btn btn-sm btn-label-secondary">
              <i class="icon-base ti tabler-id me-1"></i>Lihat KTP
            </a>
          <?php else: ?>
            <span class="ajuan-field-value text-body-secondary d-block">Belum diunggah</span>
          <?php endif; ?>
        </div>
        <div class="col-md-6">
          <span class="ajuan-field-label">Dokumen KK</span>
          <?php if (!empty($individu['foto_kk'])): ?>
            <a href="<?= base_url('ajuan/' . $ajuan['nomor_ajuan'] . '/mustahik/kk') ?>" target="_blank" class="btn btn-sm btn-label-secondary">
              <i class="icon-base ti tabler-file-text me-1"></i>Lihat KK
            </a>
          <?php else: ?>
            <span class="ajuan-field-value text-body-secondary d-block">Belum diunggah</span>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php if ($b2): ?>
  <?php
  $warnaKategoriB2 = [
    'Sangat Perlu Dibantu' => 'danger',
    'Layak Dibantu'        => 'warning',
    'Belum Layak Dibantu'  => 'secondary',
  ];
  $labelPertanyaanB2 = [
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
  $barangElektronikLabelB2 = [
    'tv' => 'Televisi',
    'hp' => 'HP',
    'kulkas' => 'Kulkas',
    'magic_com' => 'Magic Com',
    'mesin_cuci' => 'Mesin Cuci',
    'setrika' => 'Setrika Listrik',
    'dispenser' => 'Dispenser',
  ];
  ?>
  <div class="card mb-4">
    <div class="card-header">
      <h5 class="mb-0">Hasil Assessment Kelayakan (Form B2)</h5>
    </div>
    <div class="card-body">
      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <div class="ajuan-value-box">
            <span class="ajuan-field-label">Total Skor</span>
            <span class="ajuan-field-value fs-4"><?= (int) $b2['total_skor'] ?></span>
          </div>
        </div>
        <div class="col-md-6">
          <div class="ajuan-value-box">
            <span class="ajuan-field-label">Kategori Kelayakan</span>
            <span class="badge bg-label-<?= $warnaKategoriB2[$b2['kategori_kelayakan']] ?? 'secondary' ?> fs-6">
              <?= esc($b2['kategori_kelayakan']) ?>
            </span>
          </div>
        </div>
      </div>

      <button type="button" class="btn btn-sm btn-label-secondary mb-3" data-bs-toggle="collapse" data-bs-target="#detailB2">
        <i class="icon-base ti tabler-list-details me-1"></i>Lihat Rincian Jawaban
      </button>

      <div class="collapse" id="detailB2">
        <div class="row mb-3">
          <?php foreach ($labelPertanyaanB2 as $key => $label): ?>
            <?php
            // "<key>_opsi" carries the exact answer label the applicant
            // picked (posted alongside the score at submission time -
            // see FormB2Reader). Rows saved before that column existed
            // fall back to the frozen historical score->label table
            // (unavailable for q32, whose score has always been
            // ambiguous), then to the bare score as a last resort.
            $jawabanB2 = $b2[$key . '_opsi']
                ?? \App\Models\FormB2Model::SKOR_KE_LABEL_HISTORIS[$key][(int) ($b2[$key] ?? 0)]
                ?? ('Skor ' . (int) ($b2[$key] ?? 0));
            ?>
            <div class="col-md-6 mb-2">
              <span class="ajuan-field-label mb-0"><?= esc($label) ?></span>
              <span class="ajuan-field-value"><?= esc($jawabanB2) ?></span>
            </div>
          <?php endforeach; ?>
        </div>

        <h6 class="text-uppercase text-body-secondary small">Barang Elektronik</h6>
        <div class="table-responsive mb-3">
          <table class="table table-sm table-bordered">
            <thead>
              <tr>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($barangElektronikLabelB2 as $key => $label): ?>
                <?php $jumlahB2 = (int) ($b2['elektronik_' . $key . '_jumlah'] ?? 0); ?>
                <?php if ($jumlahB2 > 0): ?>
                  <tr>
                    <td><?= esc($label) ?></td>
                    <td><?= $jumlahB2 ?></td>
                    <td><?= esc($b2['elektronik_' . $key . '_status'] ?? '-') ?></td>
                  </tr>
                <?php endif; ?>
              <?php endforeach; ?>
              <?php if (!empty($b2['elektronik_lainnya_nama']) && (int) $b2['elektronik_lainnya_jumlah'] > 0): ?>
                <tr>
                  <td><?= esc($b2['elektronik_lainnya_nama']) ?></td>
                  <td><?= (int) $b2['elektronik_lainnya_jumlah'] ?></td>
                  <td><?= esc($b2['elektronik_lainnya_status'] ?? '-') ?></td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <div class="row">
          <div class="col-12 mb-2">
            <span class="ajuan-field-label">Catatan Tambahan</span>
            <span class="ajuan-field-value"><?= nl2br(esc($b2['catatan_tambahan'] ?? '-')) ?></span>
          </div>
          <div class="col-12">
            <span class="ajuan-field-label">Bersedia Dipublikasikan</span>
            <span class="ajuan-field-value"><?= ((int) $b2['bersedia_dipublikasikan'] === 1) ? 'Ya' : 'Tidak' ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php if ($lembaga): ?>
  <div class="card mb-4">
    <div class="card-header">
      <h5 class="mb-0">Data Lembaga</h5>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6 mb-3"><span class="ajuan-field-label">Nama Lembaga</span><span class="ajuan-field-value"><?= esc($lembaga['nama_lembaga']) ?></span></div>
        <div class="col-md-6 mb-3"><span class="ajuan-field-label">Nomor Legalitas (Akta/Izin Operasional/NIB)</span><span class="ajuan-field-value"><?= esc($lembaga['nomor_lembaga']) ?></span></div>
        <div class="col-md-6 mb-3"><span class="ajuan-field-label">Bidang</span><span class="ajuan-field-value"><?= esc($lembaga['bidang'] ?? '-') ?></span></div>
        <div class="col-md-6 mb-3">
          <span class="ajuan-field-label">Alamat</span>
          <span class="ajuan-field-value">
            <?= esc($lembaga['alamat_lembaga']) ?>,
            <?= esc($lembaga['nama_kelurahan'] ?? '-') ?>, <?= esc($lembaga['nama_kecamatan'] ?? '-') ?>,
            <?= esc($lembaga['nama_kabupaten'] ?? '-') ?>, <?= esc($lembaga['nama_provinsi'] ?? '-') ?>
          </span>
        </div>
        <div class="col-md-6 mb-3"><span class="ajuan-field-label">Telepon</span><span class="ajuan-field-value"><?= esc($lembaga['nomor_telepon'] ?? '-') ?></span></div>
        <div class="col-md-6 mb-3"><span class="ajuan-field-label">Email</span><span class="ajuan-field-value"><?= esc($lembaga['email'] ?? '-') ?></span></div>
        <div class="col-md-6 mb-3"><span class="ajuan-field-label">Website</span><span class="ajuan-field-value"><?= esc($lembaga['website'] ?: '-') ?></span></div>
        <div class="col-md-6 mb-3"><span class="ajuan-field-label">Sumber Pendanaan</span><span class="ajuan-field-value"><?= esc($lembaga['sumber_pendanaan'] ?: '-') ?></span></div>
        <div class="col-md-6 mb-3"><span class="ajuan-field-label">Nomor Rekening</span><span class="ajuan-field-value"><?= esc($lembaga['nomor_rekening'] ?: '-') ?></span></div>
        <div class="col-md-6 mb-3"><span class="ajuan-field-label">Nama Pemilik Rekening</span><span class="ajuan-field-value"><?= esc($lembaga['nama_pemilik_rekening'] ?: '-') ?></span></div>
        <div class="col-md-6 mb-3"><span class="ajuan-field-label">Nama Penanggung Jawab</span><span class="ajuan-field-value"><?= esc($lembaga['nama_pj'] ?? '-') ?></span></div>
        <div class="col-md-6 mb-3"><span class="ajuan-field-label">Jabatan Penanggung Jawab</span><span class="ajuan-field-value"><?= esc($lembaga['jabatan_pj'] ?? '-') ?></span></div>
      </div>
    </div>
  </div>
<?php endif; ?>

<?= view('disposisi/_hasil_card', ['judul' => 'Hasil Survey', 'data' => $survey]) ?>
<?= view('disposisi/_hasil_card', ['judul' => 'Hasil Tinjauan Kepala Divisi Program', 'data' => $kadiv]) ?>

<?= view('disposisi/_form_tinjauan', [
  'latest'      => $latestManager,
  'actionUrl'   => base_url('disposisi/manager/' . $ajuan['nomor_ajuan'] . '/store'),
  'judulForm'   => 'Formulir Tinjauan Manager',
  'judulSudah'  => 'Tinjauan Manager sudah diisi',
  'placeholder' => 'Tuliskan hasil tinjauan atas ajuan, hasil survey, dan tinjauan Kepala Divisi Program di atas...',
]) ?>

<?= view('disposisi/_riwayat_tinjauan', ['judul' => 'Riwayat Tinjauan Manager', 'riwayat' => $riwayatManager]) ?>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<?= view('disposisi/_form_tinjauan_script', [
  'placeholder' => 'Tuliskan hasil tinjauan atas ajuan, hasil survey, dan tinjauan Kepala Divisi Program di atas...',
]) ?>
<?= $this->endSection() ?>
