<?= $this->extend('layouts/main') ?>

<?= $this->section('pageStyles') ?>
<link rel="stylesheet" href="<?= base_url('assets/vendor/libs/apex-charts/apex-charts.css') ?>" />
<style>
  .dash-stat-card .avatar-initial {
    width: 3rem;
    height: 3rem;
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$dari             = $dari ?? null;
$sampai           = $sampai ?? null;
$totalDana        = $totalDana ?? 0;
$danaBuckets      = $danaBuckets ?? [];
$countPerKategori = $countPerKategori ?? [];
$countPerPilar    = $countPerPilar ?? [];
$countPerBulan    = $countPerBulan ?? [];
$tahun            = $tahun ?? date('Y');

$bulanLabel = [
  1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
];
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
  <div>
    <h4 class="mb-1">Dashboard</h4>
    <p class="text-body-secondary mb-0">Ringkasan ajuan dan penyaluran dana Lazismu Sragen.</p>
  </div>
  <form method="get" class="d-flex align-items-end gap-2 flex-wrap">
    <div>
      <label class="form-label mb-1 small">Dari Tanggal</label>
      <input type="date" name="dari" class="form-control form-control-sm" value="<?= esc($dari ?? '') ?>" />
    </div>
    <div>
      <label class="form-label mb-1 small">Sampai Tanggal</label>
      <input type="date" name="sampai" class="form-control form-control-sm" value="<?= esc($sampai ?? '') ?>" />
    </div>
    <button type="submit" class="btn btn-sm btn-primary">
      <i class="icon-base ti tabler-filter me-1"></i>Filter
    </button>
    <?php if ($dari || $sampai): ?>
      <a href="<?= base_url('dashboard') ?>" class="btn btn-sm btn-label-secondary">
        <i class="icon-base ti tabler-refresh me-1"></i>Reset
      </a>
    <?php endif; ?>
  </form>
</div>

<div class="card mb-4 bg-label-warning">
  <div class="card-body d-flex align-items-center gap-3">
    <div class="avatar avatar-lg flex-shrink-0">
      <span class="avatar-initial rounded bg-warning">
        <i class="icon-base ti tabler-report-money icon-lg"></i>
      </span>
    </div>
    <div>
      <span class="d-block">Total Dana Tersalurkan<?= $dari || $sampai ? ' (Sesuai Filter)' : '' ?></span>
      <h4 class="mb-0">Rp <?= number_format($totalDana, 0, ',', '.') ?></h4>
    </div>
  </div>
</div>

<div class="row g-4 mb-4">
  <?php
  $tiles = [
    ['label' => 'Ajuan Baru', 'count' => $countAjuanBaru ?? 0, 'icon' => 'tabler-bell', 'color' => 'warning'],
    ['label' => 'Dalam Proses', 'count' => $countAjuanProses ?? 0, 'icon' => 'tabler-hourglass', 'color' => 'info'],
    ['label' => 'Rutin Berjalan', 'count' => $countAjuanRutin ?? 0, 'icon' => 'tabler-flask', 'color' => 'primary'],
    ['label' => 'Selesai (Disetujui)', 'count' => $countAjuanSelesai ?? 0, 'icon' => 'tabler-circle-check', 'color' => 'success'],
    ['label' => 'Selesai (Ditolak)', 'count' => $countAjuanDitolak ?? 0, 'icon' => 'tabler-circle-x', 'color' => 'danger'],
  ];
  ?>
  <?php foreach ($tiles as $tile): ?>
    <div class="col-6 col-md-4 col-xl">
      <div class="card dash-stat-card h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="avatar flex-shrink-0">
            <span class="avatar-initial rounded bg-label-<?= $tile['color'] ?>">
              <i class="icon-base ti <?= $tile['icon'] ?> icon-lg"></i>
            </span>
          </div>
          <div class="overflow-hidden">
            <span class="d-block text-body-secondary text-truncate"><?= $tile['label'] ?></span>
            <h4 class="mb-0"><?= (int) $tile['count'] ?></h4>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div class="card mb-4">
  <div class="card-header">
    <h5 class="mb-0">Statistik Bulanan Ajuan (<?= (int) $tahun ?>)</h5>
  </div>
  <div class="card-body">
    <div id="chartBulanan"></div>
  </div>
</div>

<div class="row g-4 mb-4">
  <div class="col-lg-7">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="mb-0">Statistik Program</h5>
      </div>
      <div class="card-body">
        <?php if ($countPerKategori): ?>
          <div id="chartProgram"></div>
        <?php else: ?>
          <p class="text-body-secondary mb-0">Belum ada data untuk periode ini.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="mb-0">Statistik Pilar</h5>
      </div>
      <div class="card-body">
        <?php if ($countPerPilar): ?>
          <div id="chartPilar"></div>
        <?php else: ?>
          <p class="text-body-secondary mb-0">Belum ada data untuk periode ini.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<div class="row g-4">
  <?php
  $danaCards = [
    ['key' => 'Zakat', 'label' => 'Zakat', 'elId' => 'chartZakat', 'color' => '#28c76f'],
    ['key' => 'Infaq Umum', 'label' => 'Infaq Umum', 'elId' => 'chartInfaqUmum', 'color' => '#00cfe8'],
    ['key' => 'Infaq Terikat', 'label' => 'Sosial Keagamaan (Infaq Terikat)', 'elId' => 'chartSosial', 'color' => '#7367f0'],
    ['key' => 'Amil', 'label' => 'Amil', 'elId' => 'chartAmil', 'color' => '#ff9f43'],
  ];
  ?>
  <?php foreach ($danaCards as $dc): ?>
    <?php $bucketData = $danaBuckets[$dc['key']] ?? []; ?>
    <div class="col-md-6">
      <div class="card h-100">
        <div class="card-header">
          <h6 class="mb-0">Dana Tersalurkan dari <?= esc($dc['label']) ?> (Rp <?= number_format(array_sum($bucketData), 0, ',', '.') ?>)</h6>
        </div>
        <div class="card-body">
          <?php if ($bucketData): ?>
            <div id="<?= $dc['elId'] ?>"></div>
          <?php else: ?>
            <p class="text-body-secondary mb-0">Belum ada dana tersalurkan untuk kategori ini.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script src="<?= base_url('assets/vendor/libs/apex-charts/apexcharts.js') ?>"></script>
<script>
  (function () {
    var textMuted = getComputedStyle(document.documentElement).getPropertyValue('--bs-body-color') || '#6b7280';

    function barChart(elId, categories, data, color) {
      var el = document.querySelector(elId);
      if (!el) return;

      new ApexCharts(el, {
        chart: { type: 'bar', height: 300, toolbar: { show: false } },
        plotOptions: { bar: { borderRadius: 4, horizontal: true } },
        dataLabels: { enabled: false },
        series: [{ name: 'Jumlah', data: data }],
        xaxis: { categories: categories, labels: { style: { colors: textMuted } } },
        yaxis: { labels: { style: { colors: textMuted } } },
        colors: [color || '#7367f0'],
        grid: { borderColor: 'rgba(75, 70, 92, 0.1)' },
      }).render();
    }

    barChart('#chartProgram', <?= json_encode(array_keys($countPerKategori)) ?>, <?= json_encode(array_values($countPerKategori)) ?>, '#7367f0');
    barChart('#chartZakat', <?= json_encode(array_keys($danaBuckets['Zakat'] ?? [])) ?>, <?= json_encode(array_values($danaBuckets['Zakat'] ?? [])) ?>, '#28c76f');
    barChart('#chartInfaqUmum', <?= json_encode(array_keys($danaBuckets['Infaq Umum'] ?? [])) ?>, <?= json_encode(array_values($danaBuckets['Infaq Umum'] ?? [])) ?>, '#00cfe8');
    barChart('#chartSosial', <?= json_encode(array_keys($danaBuckets['Infaq Terikat'] ?? [])) ?>, <?= json_encode(array_values($danaBuckets['Infaq Terikat'] ?? [])) ?>, '#7367f0');
    barChart('#chartAmil', <?= json_encode(array_keys($danaBuckets['Amil'] ?? [])) ?>, <?= json_encode(array_values($danaBuckets['Amil'] ?? [])) ?>, '#ff9f43');

    var pilarEl = document.querySelector('#chartPilar');
    if (pilarEl) {
      new ApexCharts(pilarEl, {
        chart: { type: 'donut', height: 300 },
        series: <?= json_encode(array_values($countPerPilar)) ?>,
        labels: <?= json_encode(array_keys($countPerPilar)) ?>,
        legend: { position: 'bottom', labels: { colors: textMuted } },
        colors: ['#7367f0', '#28c76f', '#00cfe8', '#ff9f43', '#ea5455', '#82868b'],
      }).render();
    }

    var bulananEl = document.querySelector('#chartBulanan');
    if (bulananEl) {
      new ApexCharts(bulananEl, {
        chart: { type: 'bar', height: 300, toolbar: { show: false } },
        plotOptions: { bar: { borderRadius: 4, columnWidth: '45%' } },
        dataLabels: { enabled: false },
        series: [{ name: 'Ajuan', data: <?= json_encode(array_values($countPerBulan)) ?> }],
        xaxis: { categories: <?= json_encode(array_values($bulanLabel)) ?>, labels: { style: { colors: textMuted } } },
        yaxis: { labels: { style: { colors: textMuted } } },
        colors: ['#7367f0'],
        grid: { borderColor: 'rgba(75, 70, 92, 0.1)' },
      }).render();
    }
  })();
</script>
<?= $this->endSection() ?>
