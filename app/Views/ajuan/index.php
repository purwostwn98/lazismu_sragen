<?= $this->extend('layouts/main') ?>

<?= $this->section('pageStyles') ?>
<style>
  .ajuan-stat-card {
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .ajuan-stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.5rem 1.25rem rgba(0, 0, 0, 0.1);
  }

  .ajuan-stat-card.is-active {
    border-color: var(--bs-primary);
  }

  .nav-pills .nav-link .badge {
    font-weight: 600;
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<?php
$ajuanBaru    = $ajuanBaru ?? [];
$ajuanProses  = $ajuanProses ?? [];
$ajuanRutin   = $ajuanRutin ?? [];
$ajuanSelesai = $ajuanSelesai ?? [];
$ajuanDitolak = $ajuanDitolak ?? [];

$tabs = [
  ['key' => 'baru', 'label' => 'Ajuan Baru', 'icon' => 'tabler-bell', 'color' => 'warning', 'rows' => $ajuanBaru],
  ['key' => 'proses', 'label' => 'Dalam Proses', 'icon' => 'tabler-hourglass', 'color' => 'info', 'rows' => $ajuanProses],
  ['key' => 'rutin', 'label' => 'Rutin Berjalan', 'icon' => 'tabler-flask', 'color' => 'primary', 'rows' => $ajuanRutin],
  ['key' => 'selesai', 'label' => 'Selesai (Disetujui)', 'icon' => 'tabler-circle-check', 'color' => 'success', 'rows' => $ajuanSelesai],
  ['key' => 'ditolak', 'label' => 'Selesai (Ditolak)', 'icon' => 'tabler-circle-x', 'color' => 'danger', 'rows' => $ajuanDitolak],
];

$totalAjuan = array_sum(array_map(static fn($t) => count($t['rows']), $tabs));
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
  <div>
    <h4 class="mb-1"><?= esc($title ?? 'Data Ajuan') ?></h4>
    <p class="text-body-secondary mb-0">Total <?= $totalAjuan ?> ajuan tercatat.</p>
  </div>
  <a href="<?= base_url('ajuan/create') ?>" class="btn btn-primary">
    <i class="icon-base ti tabler-plus me-1"></i>Ajukan Baru
  </a>
</div>

<div class="row g-4 mb-4">
  <?php foreach ($tabs as $i => $tab): ?>
    <div class="col-6 col-md-4 col-xl">
      <div
        class="card ajuan-stat-card h-100 <?= $i === 0 ? 'is-active' : '' ?>"
        data-tab-target="#tab-<?= $tab['key'] ?>">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="avatar flex-shrink-0">
            <span class="avatar-initial rounded bg-label-<?= $tab['color'] ?>">
              <i class="icon-base ti <?= $tab['icon'] ?> icon-lg"></i>
            </span>
          </div>
          <div class="overflow-hidden">
            <span class="d-block text-body-secondary text-truncate"><?= $tab['label'] ?></span>
            <h4 class="mb-0"><?= count($tab['rows']) ?></h4>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div class="card">
  <div class="card-header">
    <ul class="nav nav-pills flex-wrap gap-1" role="tablist">
      <?php foreach ($tabs as $i => $tab): ?>
        <li class="nav-item">
          <button
            type="button"
            class="nav-link <?= $i === 0 ? 'active' : '' ?>"
            data-bs-toggle="tab"
            data-bs-target="#tab-<?= $tab['key'] ?>"
            role="tab">
            <i class="icon-base ti <?= $tab['icon'] ?> me-1"></i>
            <?= $tab['label'] ?>
            <span class="badge rounded-pill bg-label-<?= $tab['color'] ?> ms-1"><?= count($tab['rows']) ?></span>
          </button>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>

  <div class="tab-content p-0">
    <?php foreach ($tabs as $i => $tab): ?>
      <div class="tab-pane fade <?= $i === 0 ? 'show active' : '' ?>" id="tab-<?= $tab['key'] ?>" role="tabpanel">
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
              <?= view('ajuan/_rows', ['rows' => $tab['rows']]) ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
  (function () {
    document.querySelectorAll('.ajuan-stat-card[data-tab-target]').forEach(function (card) {
      card.addEventListener('click', function () {
        var targetSelector = card.getAttribute('data-tab-target');
        var trigger = document.querySelector('[data-bs-toggle="tab"][data-bs-target="' + targetSelector + '"]');
        if (trigger) {
          bootstrap.Tab.getOrCreateInstance(trigger).show();
        }
      });
    });

    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(function (trigger) {
      trigger.addEventListener('shown.bs.tab', function () {
        var targetSelector = trigger.getAttribute('data-bs-target');
        document.querySelectorAll('.ajuan-stat-card[data-tab-target]').forEach(function (card) {
          card.classList.toggle('is-active', card.getAttribute('data-tab-target') === targetSelector);
        });
      });
    });
  })();
</script>
<?= $this->endSection() ?>
