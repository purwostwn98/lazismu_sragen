<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<?php $rows = $rows ?? []; ?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
  <div>
    <h4 class="mb-1"><?= esc($title ?? 'Disposisi Kepala Divisi Program') ?></h4>
    <p class="text-body-secondary mb-0">Total <?= count($rows) ?> ajuan sudah disurvey, menunggu tinjauan Kepala Divisi Program.</p>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover table-sm datatable w-100">
      <thead>
        <tr>
          <th>#</th>
          <th>Nomor Ajuan</th>
          <th>Pemohon</th>
          <th>Kegiatan</th>
          <th>Jenis</th>
          <th>Nilai Diajukan</th>
          <th>Status</th>
          <th>Rekomendasi Surveyor</th>
          <th class="text-end text-nowrap">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $i => $a): ?>
          <?php
            $statusColor = ajuan_status_color(isset($a['status_ajuan']) ? (int) $a['status_ajuan'] : null);
            $detailUrl   = base_url('disposisi/kepala-divisi-program/' . $a['nomor_ajuan']);
            $survey      = $a['survey'] ?? null;
          ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td>
              <a href="<?= $detailUrl ?>"><?= esc($a['nomor_ajuan']) ?></a>
            </td>
            <td><?= esc($a['nama_pemohon'] ?? '-') ?></td>
            <td><?= esc($a['nama_program'] ?? '-') ?></td>
            <td><span class="badge bg-label-secondary"><?= esc($a['jenis_ajuan']) ?></span></td>
            <td>Rp <?= number_format((float) $a['nilai_diajukan'], 0, ',', '.') ?></td>
            <td><span class="badge bg-label-<?= $statusColor ?>"><?= esc($a['keterangan_status'] ?? '-') ?></span></td>
            <td>
              <?php if (!$survey): ?>
                <span class="text-body-secondary">-</span>
              <?php elseif ((int) $survey['rekomendasi'] === 1): ?>
                <span class="badge bg-label-success">Disetujui</span>
                <span class="text-body-secondary ms-1">Rp <?= number_format((float) $survey['nominal_rekomendasi'], 0, ',', '.') ?></span>
              <?php else: ?>
                <span class="badge bg-label-danger">Ditolak</span>
              <?php endif; ?>
            </td>
            <td class="text-end text-nowrap">
              <a href="<?= $detailUrl ?>" class="btn btn-icon btn-text-secondary rounded-pill">
                <i class="icon-base ti tabler-eye"></i>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?= $this->endSection() ?>
