<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>

<?php
$ajuan = $ajuan ?? [];
$logs = $logs ?? [];
?>

<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="card mb-4">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">Ajuan #<?= esc($ajuan['nomor_ajuan']) ?></h5>
        <span class="badge bg-label-info"><?= esc($ajuan['keterangan_status'] ?? '-') ?></span>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6 mb-2"><strong>Pemohon</strong><br /><?= esc($ajuan['nama_pemohon'] ?? '-') ?></div>
          <div class="col-md-6 mb-2"><strong>Kegiatan</strong><br /><?= esc($ajuan['nama_program'] ?? '-') ?></div>
          <div class="col-md-6 mb-2"><strong>Jenis Ajuan</strong><br /><?= esc($ajuan['jenis_ajuan']) ?></div>
          <div class="col-md-6 mb-2"><strong>Nilai Diajukan</strong><br />Rp <?= number_format((float) $ajuan['nilai_diajukan'], 0, ',', '.') ?></div>
          <div class="col-md-6 mb-2">
            <strong>Nilai Disetujui</strong><br />
            <?= $ajuan['nilai_disetujui'] !== null ? 'Rp ' . number_format((float) $ajuan['nilai_disetujui'], 0, ',', '.') : '-' ?>
          </div>
          <div class="col-md-6 mb-2"><strong>Tanggal Diajukan</strong><br /><?= esc($ajuan['tgl_diajukan']) ?></div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Riwayat Status</h5>
      </div>
      <div class="card-body">
        <ul class="timeline mb-0">
          <?php foreach ($logs as $log): ?>
            <li class="timeline-item timeline-item-transparent">
              <span class="timeline-point timeline-point-primary"></span>
              <div class="timeline-event">
                <div class="timeline-header mb-1">
                  <h6 class="mb-0"><?= esc($log['catatan_log'] ?: 'Perubahan status') ?></h6>
                  <small class="text-body-secondary"><?= esc($log['tanggal_log']) ?></small>
                </div>
              </div>
            </li>
          <?php endforeach; ?>
          <?php if (empty($logs)): ?>
            <li class="text-body-secondary">Belum ada riwayat.</li>
          <?php endif; ?>
        </ul>
      </div>
    </div>

    <div class="text-center mt-4">
      <a href="<?= base_url('pengajuan/status') ?>" class="btn btn-label-secondary">Cek Ajuan Lain</a>
      <a href="<?= base_url('/') ?>" class="btn btn-primary">Kembali ke Beranda</a>
    </div>
  </div>
</div>

<?= $this->endSection() ?>