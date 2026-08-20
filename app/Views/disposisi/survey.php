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
$ajuan    = $ajuan ?? [];
$individu = $individu ?? null;
$lembaga  = $lembaga ?? null;

$riwayatSurvey = $riwayatSurvey ?? [];
$latestSurvey  = $latestSurvey ?? ($riwayatSurvey[0] ?? null);

$statusColor = ajuan_status_color(isset($ajuan['status_ajuan']) ? (int) $ajuan['status_ajuan'] : null);
?>

<div class="card mb-4">
  <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div class="d-flex align-items-center gap-3">
      <div class="avatar avatar-lg flex-shrink-0">
        <span class="avatar-initial rounded bg-label-<?= $statusColor ?>">
          <i class="icon-base ti tabler-map-pin-check icon-lg"></i>
        </span>
      </div>
      <div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <h4 class="mb-0">Survey Ajuan #<?= esc($ajuan['nomor_ajuan']) ?></h4>
          <span class="badge bg-label-<?= $statusColor ?>"><?= esc($ajuan['keterangan_status'] ?? '-') ?></span>
          <span class="badge bg-label-secondary"><?= esc($ajuan['jenis_ajuan']) ?></span>
        </div>
        <p class="text-body-secondary mb-0">
          <?= esc($ajuan['nama_pemohon'] ?? '-') ?> &middot; <?= esc($ajuan['nama_program'] ?? '-') ?>
        </p>
      </div>
    </div>
    <a href="<?= base_url('disposisi/surveyor') ?>" class="btn btn-label-secondary btn-sm">
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
      <div class="col-12">
        <span class="ajuan-field-label">Deskripsi</span>
        <span class="ajuan-field-value"><?= nl2br(esc($ajuan['deskripsi_ajuan'])) ?></span>
      </div>
    </div>
  </div>
</div>

<?php if ($individu): ?>
  <div class="card mb-4">
    <div class="card-header">
      <h5 class="mb-0">Data Mustahik (Individu)</h5>
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

<?php if ($lembaga): ?>
  <div class="card mb-4">
    <div class="card-header">
      <h5 class="mb-0">Data Lembaga</h5>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-4 mb-2"><span class="ajuan-field-label">Nama Lembaga</span><span class="ajuan-field-value"><?= esc($lembaga['nama_lembaga']) ?></span></div>
        <div class="col-md-4 mb-2"><span class="ajuan-field-label">Nomor Lembaga</span><span class="ajuan-field-value"><?= esc($lembaga['nomor_lembaga']) ?></span></div>
        <div class="col-md-4 mb-2"><span class="ajuan-field-label">Alamat</span><span class="ajuan-field-value"><?= esc($lembaga['alamat_lembaga']) ?></span></div>
      </div>
    </div>
  </div>
<?php endif; ?>

<?= view('disposisi/_form_tinjauan', [
  'latest'      => $latestSurvey,
  'actionUrl'   => base_url('disposisi/survey/' . $ajuan['nomor_ajuan'] . '/store'),
  'judulForm'   => 'Formulir Hasil Survey',
  'judulSudah'  => 'Hasil survey sudah diisi',
  'placeholder' => 'Tuliskan hasil survey di lokasi... Sertakan catatan penting, golongan mustahik, rekomendasi sumber dana, dan hal-hal lain yang relevan.',
]) ?>

<?= view('disposisi/_riwayat_tinjauan', ['judul' => 'Riwayat Survey', 'riwayat' => $riwayatSurvey]) ?>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<?= view('disposisi/_form_tinjauan_script', [
  'placeholder' => 'Tuliskan hasil survey di lokasi... Sertakan catatan penting, golongan mustahik, rekomendasi sumber dana, dan hal-hal lain yang relevan.',
]) ?>
<?= $this->endSection() ?>
