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

<?= $this->include('partials/alerts') ?>

<?php
$totalDalamAlur        = $totalDalamAlur ?? 0;
$belumDisurvey         = $belumDisurvey ?? 0;
$sudahDisurvey          = $sudahDisurvey ?? 0;
$menungguKadiv          = $menungguKadiv ?? 0;
$menungguManager        = $menungguManager ?? 0;
$perluBadanPengurus     = $perluBadanPengurus ?? 0;
$menungguBadanPengurus  = $menungguBadanPengurus ?? 0;
$selesaiTanpaBadan      = $selesaiTanpaBadan ?? 0;
$selesaiDenganBadan     = $selesaiDenganBadan ?? 0;
$jumlahPerTahap         = $jumlahPerTahap ?? [];
$rekapTahap             = $rekapTahap ?? [];
$aktivitasTerbaru       = $aktivitasTerbaru ?? [];

$totalSelesai = $selesaiTanpaBadan + $selesaiDenganBadan;

$stageMeta = [
  'Surveyor'               => ['icon' => 'tabler-map-pin-check', 'color' => 'warning',   'url' => 'disposisi/surveyor',              'detailUrl' => 'disposisi/survey/'],
  'Kepala Divisi Program'  => ['icon' => 'tabler-briefcase',      'color' => 'info',      'url' => 'disposisi/kepala-divisi-program', 'detailUrl' => 'disposisi/kepala-divisi-program/'],
  'Manager'                => ['icon' => 'tabler-user-star',      'color' => 'primary',   'url' => 'disposisi/manager',               'detailUrl' => 'disposisi/manager/'],
  'Badan Pengurus'         => ['icon' => 'tabler-gavel',          'color' => 'dark',      'url' => 'disposisi/badan-pengurus',         'detailUrl' => 'disposisi/badan-pengurus/'],
];

$menungguPerTahap = [
  'Surveyor'              => $belumDisurvey,
  'Kepala Divisi Program' => $menungguKadiv,
  'Manager'                => $menungguManager,
  'Badan Pengurus'         => $menungguBadanPengurus,
];
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
  <div>
    <h4 class="mb-1">Dashboard Disposisi</h4>
    <p class="text-body-secondary mb-0">Ringkasan progres tinjauan berjenjang ajuan: Surveyor &rarr; Kepala Divisi Program &rarr; Manager &rarr; Badan Pengurus.</p>
  </div>
</div>

<div class="row g-4 mb-4">
  <?php
  $tiles = [
    ['label' => 'Ajuan Dalam Alur Disposisi', 'count' => $totalDalamAlur, 'icon' => 'tabler-list-details', 'color' => 'secondary'],
    ['label' => 'Belum Disurvey', 'count' => $belumDisurvey, 'icon' => 'tabler-map-pin-exclamation', 'color' => 'danger'],
    ['label' => 'Sudah Disurvey', 'count' => $sudahDisurvey, 'icon' => 'tabler-map-pin-check', 'color' => 'warning'],
    ['label' => 'Menunggu Badan Pengurus', 'count' => $menungguBadanPengurus, 'icon' => 'tabler-gavel', 'color' => 'dark'],
    ['label' => 'Selesai Tinjauan', 'count' => $totalSelesai, 'icon' => 'tabler-circle-check', 'color' => 'success'],
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

<div class="row g-4 mb-4">
  <?php foreach ($stageMeta as $stage => $meta): ?>
    <?php $rekap = $rekapTahap[$stage] ?? ['disetujui' => 0, 'ditolak' => 0, 'nominal' => 0]; ?>
    <div class="col-md-6 col-xl-3">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex align-items-center gap-3 mb-3">
            <div class="avatar flex-shrink-0">
              <span class="avatar-initial rounded bg-label-<?= $meta['color'] ?>">
                <i class="icon-base ti <?= $meta['icon'] ?> icon-lg"></i>
              </span>
            </div>
            <div class="overflow-hidden">
              <span class="d-block text-body-secondary text-truncate small"><?= esc($stage) ?></span>
              <h5 class="mb-0"><?= (int) ($jumlahPerTahap[$stage] ?? 0) ?> diproses</h5>
            </div>
          </div>
          <div class="d-flex align-items-center justify-content-between small mb-1">
            <span class="text-body-secondary">Menunggu tindak lanjut</span>
            <span class="fw-medium"><?= (int) $menungguPerTahap[$stage] ?></span>
          </div>
          <div class="d-flex align-items-center justify-content-between small mb-1">
            <span class="text-body-secondary"><i class="icon-base ti tabler-thumb-up text-success me-1"></i>Disetujui</span>
            <span class="fw-medium"><?= (int) $rekap['disetujui'] ?></span>
          </div>
          <div class="d-flex align-items-center justify-content-between small mb-3">
            <span class="text-body-secondary"><i class="icon-base ti tabler-thumb-down text-danger me-1"></i>Ditolak</span>
            <span class="fw-medium"><?= (int) $rekap['ditolak'] ?></span>
          </div>
          <a href="<?= base_url($meta['url']) ?>" class="btn btn-sm btn-label-<?= $meta['color'] ?> w-100">
            Lihat Worklist <i class="icon-base ti tabler-arrow-right ms-1"></i>
          </a>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php if ($perluBadanPengurus > 0): ?>
  <div class="alert alert-warning d-flex align-items-center gap-2 mb-4" role="alert">
    <i class="icon-base ti tabler-alert-triangle"></i>
    <div>
      <?= (int) $perluBadanPengurus ?> ajuan bernilai di atas Rp 5.000.000 memerlukan tinjauan Badan Pengurus,
      <?= (int) $menungguBadanPengurus ?> di antaranya masih menunggu.
    </div>
  </div>
<?php endif; ?>

<div class="row g-4 mb-4">
  <div class="col-lg-7">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="mb-0">Jumlah Ajuan Diproses per Tahap</h5>
      </div>
      <div class="card-body">
        <div id="chartTahap"></div>
      </div>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="mb-0">Alur Antrean</h5>
      </div>
      <div class="card-body">
        <?php
        $antrean = [
          ['label' => 'Belum Disurvey', 'count' => $belumDisurvey, 'color' => 'danger'],
          ['label' => 'Menunggu Kepala Divisi Program', 'count' => $menungguKadiv, 'color' => 'warning'],
          ['label' => 'Menunggu Manager', 'count' => $menungguManager, 'color' => 'info'],
          ['label' => 'Menunggu Badan Pengurus', 'count' => $menungguBadanPengurus, 'color' => 'dark'],
          ['label' => 'Selesai Tinjauan', 'count' => $totalSelesai, 'color' => 'success'],
        ];
        $maxAntrean = max(1, max(array_column($antrean, 'count')));
        ?>
        <?php foreach ($antrean as $a): ?>
          <div class="mb-3">
            <div class="d-flex align-items-center justify-content-between small mb-1">
              <span><?= esc($a['label']) ?></span>
              <span class="fw-medium"><?= (int) $a['count'] ?></span>
            </div>
            <div class="progress" style="height: 8px;">
              <div class="progress-bar bg-<?= $a['color'] ?>" style="width: <?= (int) round($a['count'] / $maxAntrean * 100) ?>%"></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h5 class="mb-0">Aktivitas Disposisi Terbaru</h5>
  </div>
  <div class="table-responsive">
    <table class="table table-hover table-sm">
      <thead>
        <tr>
          <th>Nomor Ajuan</th>
          <th>Pemohon / Kegiatan</th>
          <th>Tahap</th>
          <th>Rekomendasi</th>
          <th>Petugas</th>
          <th>Tanggal</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$aktivitasTerbaru): ?>
          <tr>
            <td colspan="6" class="text-center text-body-secondary py-4">Belum ada aktivitas disposisi.</td>
          </tr>
        <?php endif; ?>
        <?php foreach ($aktivitasTerbaru as $act): ?>
          <?php $meta = $stageMeta[$act['oleh']] ?? ['icon' => 'tabler-list-check', 'color' => 'secondary', 'detailUrl' => 'disposisi/surveyor/']; ?>
          <tr>
            <td>
              <a href="<?= base_url($meta['detailUrl'] . $act['nomor_ajuan']) ?>"><?= esc($act['nomor_ajuan']) ?></a>
            </td>
            <td>
              <?= esc($act['nama_pemohon'] ?? '-') ?>
              <span class="text-body-secondary d-block small"><?= esc($act['nama_program'] ?? '-') ?></span>
            </td>
            <td>
              <span class="badge bg-label-<?= $meta['color'] ?>">
                <i class="icon-base ti <?= $meta['icon'] ?> me-1"></i><?= esc($act['oleh']) ?>
              </span>
            </td>
            <td>
              <?php if ((int) $act['rekomendasi'] === 1): ?>
                <span class="badge bg-label-success">Disetujui</span>
                <span class="text-body-secondary d-block small">Rp <?= number_format((float) $act['nominal_rekomendasi'], 0, ',', '.') ?></span>
              <?php else: ?>
                <span class="badge bg-label-danger">Ditolak</span>
              <?php endif; ?>
            </td>
            <td><?= esc($act['nama_petugas'] ?? '-') ?></td>
            <td class="text-nowrap"><?= format_tanggal_indo($act['created_at'] ?? null, true) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script src="<?= base_url('assets/vendor/libs/apex-charts/apexcharts.js') ?>"></script>
<script>
  (function () {
    var textMuted = getComputedStyle(document.documentElement).getPropertyValue('--bs-body-color') || '#6b7280';
    var el = document.querySelector('#chartTahap');

    if (el) {
      new ApexCharts(el, {
        chart: { type: 'bar', height: 300, toolbar: { show: false } },
        plotOptions: { bar: { borderRadius: 4, horizontal: true } },
        dataLabels: { enabled: false },
        series: [{ name: 'Ajuan Diproses', data: <?= json_encode(array_values($jumlahPerTahap)) ?> }],
        xaxis: { categories: <?= json_encode(array_keys($jumlahPerTahap)) ?>, labels: { style: { colors: textMuted } } },
        yaxis: { labels: { style: { colors: textMuted } } },
        colors: ['#7367f0'],
        grid: { borderColor: 'rgba(75, 70, 92, 0.1)' },
      }).render();
    }
  })();
</script>
<?= $this->endSection() ?>
