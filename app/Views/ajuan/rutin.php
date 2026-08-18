<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<?php $rows = $rows ?? []; ?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
  <div>
    <h4 class="mb-1"><?= esc($title ?? 'Ajuan Rutin') ?></h4>
    <p class="text-body-secondary mb-0">Total <?= count($rows) ?> ajuan berstatus rutin.</p>
  </div>
  <a href="<?= base_url('ajuan/create') ?>" class="btn btn-primary">
    <i class="icon-base ti tabler-plus me-1"></i>Ajukan Baru
  </a>
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
          <th class="text-end text-nowrap">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?= view('ajuan/_rows', ['rows' => $rows, 'from' => 'rutin']) ?>
      </tbody>
    </table>
  </div>
</div>

<?= $this->endSection() ?>
