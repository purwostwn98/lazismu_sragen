<?= $this->extend('layouts/main') ?>

<?= $this->section('pageStyles') ?>
<link rel="stylesheet" href="<?= base_url('assets/vendor/libs/apex-charts/apex-charts.css') ?>" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$tahunFilter         = $tahunFilter ?? 'all';
$tahunList           = $tahunList ?? [];
$ajuanPerPilar       = $ajuanPerPilar ?? [];
$ajuanPerKecamatan   = $ajuanPerKecamatan ?? [];
$kabupatenTerlayani  = $kabupatenTerlayani ?? 0;
$danaPerPilar        = $danaPerPilar ?? [];
$danaPerKecamatan    = $danaPerKecamatan ?? [];
$grandTotalDana      = $grandTotalDana ?? 0;
$statistikPilar      = $statistikPilar ?? [];
$tahunAjuanList      = $tahunAjuanList ?? [];
$pilarList           = $pilarList ?? [];
$trenAjuanMatrix     = $trenAjuanMatrix ?? [];
$tahunDanaList       = $tahunDanaList ?? [];
$trenDanaMatrix      = $trenDanaMatrix ?? [];
$rekomendasiAnggaran = $rekomendasiAnggaran ?? [];

// Fixed categorical color order, shared with the main dashboard's pilar donut
// so "pilar" identity stays consistent across both pages.
$pilarColors = ['#7367f0', '#28c76f', '#00cfe8', '#ff9f43', '#ea5455', '#82868b'];

$totalSemuaAjuan  = array_sum(array_column($ajuanPerPilar, 'jumlah'));
$totalDisetujuiAll = array_sum(array_column($statistikPilar, 'total_disetujui'));
$approvalAll      = $totalSemuaAjuan > 0 ? round($totalDisetujuiAll / $totalSemuaAjuan * 100, 1) : 0;
$labelFilter      = $tahunFilter === 'all' ? 'Semua Tahun' : 'Tahun ' . $tahunFilter;
$tahunProyeksi    = (int) date('Y') + 1;
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
  <div>
    <h4 class="mb-1"><i class="icon-base ti tabler-chart-bar me-1"></i>Dashboard Analitik &amp; Perencanaan Anggaran</h4>
    <p class="text-body-secondary mb-0">
      Data mengikuti filter tahun — <strong class="text-primary"><?= esc($labelFilter) ?></strong>
    </p>
  </div>
  <a href="<?= base_url('dashboard') ?>" class="btn btn-label-secondary btn-sm">
    <i class="icon-base ti tabler-arrow-left me-1"></i>Dashboard Utama
  </a>
</div>

<!-- Filter tahun -->
<div class="card mb-4">
  <div class="card-body d-flex align-items-center flex-wrap gap-2 py-3">
    <span class="fw-medium text-body-secondary me-1">
      <i class="icon-base ti tabler-calendar me-1"></i>Filter Tahun:
    </span>
    <a href="<?= base_url('dashboard/analitik') ?>" class="btn btn-sm rounded-pill <?= $tahunFilter === 'all' ? 'btn-primary' : 'btn-outline-secondary' ?>">
      Semua Tahun
    </a>
    <?php foreach (array_reverse($tahunList) as $thn): ?>
      <a href="<?= base_url('dashboard/analitik') ?>?tahun=<?= $thn ?>" class="btn btn-sm rounded-pill <?= $tahunFilter === $thn ? 'btn-primary' : 'btn-outline-secondary' ?>">
        <?= $thn ?>
      </a>
    <?php endforeach; ?>
  </div>
</div>

<!-- KPI cards -->
<div class="row g-4 mb-4">
  <div class="col-6 col-md-3">
    <div class="card h-100">
      <div class="card-body text-center py-4">
        <div class="mb-2 text-primary"><i class="icon-base ti tabler-inbox" style="font-size: 2rem;"></i></div>
        <h3 class="text-primary mb-1"><?= number_format($totalSemuaAjuan) ?></h3>
        <div class="text-body-secondary text-90">Total Ajuan Masuk</div>
        <div class="text-body-secondary text-80"><?= esc($labelFilter) ?></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card h-100">
      <div class="card-body text-center py-4">
        <div class="mb-2 text-success"><i class="icon-base ti tabler-circle-check" style="font-size: 2rem;"></i></div>
        <h3 class="text-success mb-1"><?= $approvalAll ?>%</h3>
        <div class="text-body-secondary text-90">Tingkat Persetujuan</div>
        <div class="text-body-secondary text-80"><?= esc($labelFilter) ?></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card h-100">
      <div class="card-body text-center py-4">
        <div class="mb-2 text-warning"><i class="icon-base ti tabler-hand-holding-dollar" style="font-size: 2rem;"></i></div>
        <h3 class="text-warning mb-1">Rp <?= number_format($grandTotalDana, 0, ',', '.') ?></h3>
        <div class="text-body-secondary text-90">Total Dana Tersalurkan</div>
        <div class="text-body-secondary text-80"><?= esc($labelFilter) ?></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card h-100">
      <div class="card-body text-center py-4">
        <div class="mb-2 text-info"><i class="icon-base ti tabler-map-pin" style="font-size: 2rem;"></i></div>
        <h3 class="text-info mb-1"><?= (int) $kabupatenTerlayani ?></h3>
        <div class="text-body-secondary text-90">Kab/Kota Terlayani</div>
        <div class="text-body-secondary text-80"><?= esc($labelFilter) ?></div>
      </div>
    </div>
  </div>
</div>

<!-- ================================================================== -->
<!-- SECTION 1 — AJUAN TERBANYAK -->
<!-- ================================================================== -->
<h5 class="mb-3">
  <span class="badge bg-primary rounded-pill me-2">1</span>
  Ajuan Terbanyak &mdash; Per Pilar &amp; Per Daerah
  <span class="badge bg-label-primary ms-1"><?= esc($labelFilter) ?></span>
</h5>

<div class="row g-4 mb-4">
  <div class="col-lg-5">
    <div class="card h-100">
      <div class="card-header">
        <h6 class="mb-0"><i class="icon-base ti tabler-chart-donut-2 me-1"></i>Distribusi Ajuan per Pilar</h6>
      </div>
      <div class="card-body">
        <?php if ($totalSemuaAjuan > 0): ?>
          <div id="chartAjuanPilar"></div>
          <div class="mt-3">
            <?php foreach ($ajuanPerPilar as $i => $row): ?>
              <?php $pct = $totalSemuaAjuan > 0 ? round($row['jumlah'] / $totalSemuaAjuan * 100, 1) : 0; ?>
              <div class="d-flex align-items-center mb-1">
                <span class="me-2 flex-shrink-0" style="width:12px;height:12px;border-radius:3px;background:<?= $pilarColors[$i % count($pilarColors)] ?>;"></span>
                <span class="text-90 flex-grow-1"><?= esc($row['nama_pilar']) ?></span>
                <span class="fw-semibold text-90 ms-2"><?= number_format($row['jumlah']) ?></span>
                <span class="text-body-secondary text-80 ms-2">(<?= $pct ?>%)</span>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="text-body-secondary mb-0">Belum ada data untuk periode ini.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="col-lg-7">
    <div class="card h-100">
      <div class="card-header">
        <h6 class="mb-0"><i class="icon-base ti tabler-map me-1"></i>Top 20 Kecamatan &mdash; Jumlah Ajuan</h6>
      </div>
      <div class="card-body">
        <?php if ($ajuanPerKecamatan): ?>
          <div id="chartAjuanKec"></div>
        <?php else: ?>
          <p class="text-body-secondary mb-0">Belum ada data untuk periode ini.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- ================================================================== -->
<!-- SECTION 2 — DANA TERSALURKAN -->
<!-- ================================================================== -->
<h5 class="mb-3">
  <span class="badge bg-success rounded-pill me-2">2</span>
  Dana Tersalurkan (Tasaruf) &mdash; Per Pilar &amp; Per Daerah
  <span class="badge bg-label-success ms-1"><?= esc($labelFilter) ?></span>
</h5>

<div class="row g-4 mb-4">
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header">
        <h6 class="mb-0"><i class="icon-base ti tabler-chart-bar me-1"></i>Nominal Tersalurkan per Pilar</h6>
      </div>
      <div class="card-body">
        <?php if ($grandTotalDana > 0): ?>
          <div id="chartDanaPilar"></div>
          <hr>
          <?php foreach ($danaPerPilar as $i => $row): ?>
            <?php $pct = $grandTotalDana > 0 ? round($row['total_tersalurkan'] / $grandTotalDana * 100, 1) : 0; ?>
            <div class="mb-2">
              <div class="d-flex justify-content-between text-85 mb-1">
                <span class="fw-medium"><?= esc($row['nama_pilar']) ?></span>
                <span>Rp <?= number_format($row['total_tersalurkan'], 0, ',', '.') ?> <span class="text-body-secondary">(<?= $pct ?>%)</span></span>
              </div>
              <div class="progress" style="height: 8px;">
                <div class="progress-bar" role="progressbar" style="width:<?= $pct ?>%;background:<?= $pilarColors[$i % count($pilarColors)] ?>"></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-body-secondary mb-0">Belum ada dana tersalurkan untuk periode ini.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header">
        <h6 class="mb-0"><i class="icon-base ti tabler-map me-1"></i>Top 20 Kecamatan &mdash; Dana Tersalurkan</h6>
      </div>
      <div class="card-body">
        <?php if ($danaPerKecamatan): ?>
          <div id="chartDanaKec"></div>
        <?php else: ?>
          <p class="text-body-secondary mb-0">Belum ada dana tersalurkan untuk periode ini.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="col-lg-6 col-md-9 mx-auto">
    <div class="card">
      <div class="card-header">
        <h6 class="mb-0"><i class="icon-base ti tabler-chart-pie me-1"></i>Persentase Dana Tersalurkan per Pilar</h6>
      </div>
      <div class="card-body">
        <?php if ($grandTotalDana > 0): ?>
          <div id="chartDanaPiePilar"></div>
          <div class="mt-3">
            <?php foreach ($danaPerPilar as $i => $row): ?>
              <?php $pct2c = $grandTotalDana > 0 ? round($row['total_tersalurkan'] / $grandTotalDana * 100, 1) : 0; ?>
              <div class="d-flex align-items-center mb-1">
                <span class="me-2 flex-shrink-0" style="width:12px;height:12px;border-radius:3px;background:<?= $pilarColors[$i % count($pilarColors)] ?>;"></span>
                <span class="text-90 flex-grow-1"><?= esc($row['nama_pilar']) ?></span>
                <span class="text-90 fw-semibold ms-2">Rp <?= number_format($row['total_tersalurkan'], 0, ',', '.') ?></span>
                <span class="badge bg-label-success ms-2" style="min-width:42px;"><?= $pct2c ?>%</span>
              </div>
            <?php endforeach; ?>
            <div class="d-flex align-items-center mt-2 pt-2 border-top">
              <span class="flex-grow-1 fw-semibold text-90">Total</span>
              <span class="fw-semibold text-success text-90">Rp <?= number_format($grandTotalDana, 0, ',', '.') ?></span>
              <span class="badge bg-label-dark ms-2" style="min-width:42px;">100%</span>
            </div>
          </div>
        <?php else: ?>
          <p class="text-body-secondary mb-0">Belum ada dana tersalurkan untuk periode ini.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- ================================================================== -->
<!-- SECTION 3 — PERENCANAAN ANGGARAN -->
<!-- ================================================================== -->
<h5 class="mb-1">
  <span class="badge bg-warning rounded-pill me-2">3</span>
  Informasi Perencanaan Anggaran
</h5>
<p class="text-body-secondary text-85 mb-3">
  <i class="icon-base ti tabler-info-circle me-1"></i>
  <strong>Grafik tren (3a &amp; 3b)</strong> selalu menampilkan data historis sejak <?= (int) ($tahunAjuanList[0] ?? date('Y')) ?> — tidak dipengaruhi filter tahun.
  <strong>Tabel statistik &amp; rekomendasi</strong> mengikuti filter:
  <span class="badge bg-label-secondary"><?= esc($labelFilter) ?></span>
</p>

<div class="row g-4 mb-4">
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header">
        <h6 class="mb-0">
          <i class="icon-base ti tabler-chart-line me-1"></i>Tren Jumlah Ajuan per Pilar
          <small class="text-body-secondary fw-normal">(<?= (int) ($tahunAjuanList[0] ?? date('Y')) ?> &mdash; sekarang)</small>
        </h6>
      </div>
      <div class="card-body">
        <?php if ($tahunAjuanList): ?>
          <div id="chartTrenAjuan"></div>
        <?php else: ?>
          <p class="text-body-secondary mb-0">Belum ada data historis.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header">
        <h6 class="mb-0">
          <i class="icon-base ti tabler-chart-area-line me-1"></i>Tren Dana Tersalurkan per Pilar
          <small class="text-body-secondary fw-normal">(<?= (int) ($tahunDanaList[0] ?? date('Y')) ?> &mdash; sekarang)</small>
        </h6>
      </div>
      <div class="card-body">
        <?php if ($tahunDanaList): ?>
          <div id="chartTrenDana"></div>
        <?php else: ?>
          <p class="text-body-secondary mb-0">Belum ada data historis.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- 3c. Tabel statistik per pilar -->
<div class="card mb-4">
  <div class="card-header d-flex align-items-center justify-content-between">
    <h6 class="mb-0"><i class="icon-base ti tabler-table me-1"></i>Statistik Lengkap per Pilar</h6>
    <span class="badge bg-label-dark"><?= esc($labelFilter) ?></span>
  </div>
  <div class="table-responsive">
    <table class="table table-sm table-hover table-bordered mb-0">
      <thead class="table-light">
        <tr class="text-center">
          <th class="text-start" style="min-width:130px">Pilar</th>
          <th>Total<br>Ajuan</th>
          <th>Disetujui</th>
          <th>Ditolak</th>
          <th>Pending</th>
          <th>Approval<br>Rate</th>
          <th>Rata-rata<br>Nilai Disetujui</th>
          <th>Maks Nilai</th>
          <th>Min Nilai</th>
          <th>Total<br>Nilai Disetujui</th>
          <th>Total<br>Tersalurkan</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($statistikPilar as $i => $row): ?>
          <?php
          $ar      = $row['total_ajuan'] > 0 ? round($row['total_disetujui'] / $row['total_ajuan'] * 100, 1) : 0;
          $arClass = $ar >= 60 ? 'success' : ($ar >= 30 ? 'warning' : 'danger');
          ?>
          <tr>
            <td>
              <span class="badge rounded-pill text-white" style="background:<?= $pilarColors[$i % count($pilarColors)] ?>">
                <?= esc($row['nama_pilar']) ?>
              </span>
            </td>
            <td class="text-center"><?= number_format($row['total_ajuan']) ?></td>
            <td class="text-center text-success fw-semibold"><?= number_format($row['total_disetujui']) ?></td>
            <td class="text-center text-danger"><?= number_format($row['total_ditolak']) ?></td>
            <td class="text-center text-warning"><?= number_format($row['total_pending']) ?></td>
            <td class="text-center"><span class="badge bg-label-<?= $arClass ?>"><?= $ar ?>%</span></td>
            <td class="text-end">Rp <?= number_format($row['avg_nilai_disetujui'], 0, ',', '.') ?></td>
            <td class="text-end">Rp <?= number_format($row['maks_nilai'], 0, ',', '.') ?></td>
            <td class="text-end">Rp <?= number_format($row['min_nilai'], 0, ',', '.') ?></td>
            <td class="text-end text-success">Rp <?= number_format($row['total_nilai_disetujui'], 0, ',', '.') ?></td>
            <td class="text-end fw-semibold">Rp <?= number_format($row['total_tersalurkan'], 0, ',', '.') ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$statistikPilar): ?>
          <tr><td colspan="11" class="text-center text-body-secondary py-4">Belum ada data untuk periode ini.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- 3d. Tabel rekomendasi anggaran -->
<div class="card border-warning mb-4">
  <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
    <span>
      <i class="icon-base ti tabler-bulb me-1"></i>
      <strong>Rekomendasi Anggaran Tahun <?= $tahunProyeksi ?> per Pilar</strong>
      <small class="d-none d-md-inline ms-1 text-body-secondary">(growth rate dari tren dana 2 tahun penuh terakhir &mdash; tidak dipengaruhi filter)</small>
    </span>
    <span class="badge bg-label-dark">Statistik pilar: <?= esc($labelFilter) ?></span>
  </div>
  <div class="table-responsive">
    <table class="table table-sm table-hover table-bordered mb-0">
      <thead class="table-light">
        <tr class="text-center">
          <th class="text-start" style="min-width:130px">Pilar</th>
          <th>Total Ajuan</th>
          <th>Approval Rate</th>
          <th>Rata-rata<br>Nilai Disetujui</th>
          <th>Total<br>Nilai Disetujui</th>
          <th>Total<br>Tersalurkan</th>
          <th>Growth Rate<br>(YoY)</th>
          <th class="text-primary">Proyeksi Anggaran<br><?= $tahunProyeksi ?></th>
          <th>Catatan</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rekomendasiAnggaran as $i => $rec): ?>
          <tr>
            <td>
              <span class="badge rounded-pill text-white" style="background:<?= $pilarColors[$i % count($pilarColors)] ?>">
                <?= esc($rec['nama_pilar']) ?>
              </span>
            </td>
            <td class="text-center"><?= number_format($rec['total_ajuan']) ?></td>
            <td class="text-center">
              <?php $ar2 = $rec['approval_rate']; $cls = $ar2 >= 60 ? 'success' : ($ar2 >= 30 ? 'warning' : 'danger'); ?>
              <span class="badge bg-label-<?= $cls ?>"><?= $ar2 ?>%</span>
            </td>
            <td class="text-end">Rp <?= number_format($rec['avg_disetujui'], 0, ',', '.') ?></td>
            <td class="text-end text-success">Rp <?= number_format($rec['total_disetujui'], 0, ',', '.') ?></td>
            <td class="text-end">Rp <?= number_format($rec['total_tersalurkan'], 0, ',', '.') ?></td>
            <td class="text-center">
              <?php if ($rec['growth_rate'] > 0): ?>
                <span class="text-success fw-semibold"><i class="icon-base ti tabler-trending-up"></i> <?= $rec['growth_rate'] ?>%</span>
              <?php elseif ($rec['growth_rate'] < 0): ?>
                <span class="text-danger fw-semibold"><i class="icon-base ti tabler-trending-down"></i> <?= abs($rec['growth_rate']) ?>%</span>
              <?php else: ?>
                <span class="text-body-secondary fw-semibold"><i class="icon-base ti tabler-minus"></i> &mdash;</span>
              <?php endif; ?>
            </td>
            <td class="text-end fw-semibold text-primary">
              Rp <?= number_format($rec['proyeksi_anggaran'], 0, ',', '.') ?>
            </td>
            <td class="text-85">
              <?php if ($rec['growth_rate'] > 20): ?>
                <span class="text-success"><i class="icon-base ti tabler-alert-circle"></i> Permintaan tinggi, pertimbangkan kenaikan signifikan</span>
              <?php elseif ($rec['growth_rate'] > 0): ?>
                <span class="text-info"><i class="icon-base ti tabler-circle-check"></i> Tren positif, pertahankan alokasi</span>
              <?php elseif ($rec['growth_rate'] < -10): ?>
                <span class="text-danger"><i class="icon-base ti tabler-alert-triangle"></i> Tren menurun, evaluasi program</span>
              <?php elseif ($rec['total_tersalurkan'] == 0): ?>
                <span class="text-body-secondary"><i class="icon-base ti tabler-help-circle"></i> Belum ada data tersalurkan</span>
              <?php else: ?>
                <span class="text-warning"><i class="icon-base ti tabler-circle-minus"></i> Stabil, gunakan rata-rata historis</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$rekomendasiAnggaran): ?>
          <tr><td colspan="9" class="text-center text-body-secondary py-4">Belum ada data untuk periode ini.</td></tr>
        <?php endif; ?>
      </tbody>
      <?php if ($rekomendasiAnggaran): ?>
        <tfoot class="table-light">
          <tr>
            <td colspan="7" class="text-end fw-semibold">Total Proyeksi Anggaran <?= $tahunProyeksi ?> :</td>
            <td class="text-end fw-semibold text-primary">
              Rp <?= number_format(array_sum(array_column($rekomendasiAnggaran, 'proyeksi_anggaran')), 0, ',', '.') ?>
            </td>
            <td></td>
          </tr>
        </tfoot>
      <?php endif; ?>
    </table>
  </div>
  <div class="card-footer bg-transparent small text-body-secondary">
    <i class="icon-base ti tabler-info-circle me-1"></i>
    <strong>Metodologi:</strong>
    Proyeksi dihitung menggunakan <em>growth rate year-on-year</em> dari 2 tahun penuh terakhir (T-1 vs T-2).
    Jika hanya 1 tahun data tersedia, diasumsikan kenaikan +10%. Jika belum ada data tersalurkan, proyeksi berdasarkan
    rata-rata nilai disetujui &times; jumlah ajuan disetujui (sesuai filter). Angka ini bersifat indikatif.
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script src="<?= base_url('assets/vendor/libs/apex-charts/apexcharts.js') ?>"></script>
<script>
  (function () {
    var textMuted = getComputedStyle(document.documentElement).getPropertyValue('--bs-body-color') || '#6b7280';
    var gridColor = 'rgba(75, 70, 92, 0.1)';
    var COLORS = <?= json_encode($pilarColors) ?>;

    function fmtRupiah(v) {
      if (v >= 1e9) return 'Rp ' + (v / 1e9).toFixed(1) + ' M';
      if (v >= 1e6) return 'Rp ' + (v / 1e6).toFixed(1) + ' Jt';
      if (v >= 1e3) return 'Rp ' + (v / 1e3).toFixed(1) + ' Rb';
      return 'Rp ' + v;
    }

    function render(elId, options) {
      var el = document.querySelector(elId);
      if (!el) return;
      new ApexCharts(el, options).render();
    }

    // 1a. Donut Ajuan per Pilar
    render('#chartAjuanPilar', {
      chart: { type: 'donut', height: 260 },
      series: <?= json_encode(array_values(array_map(static fn ($r) => (int) $r['jumlah'], $ajuanPerPilar))) ?>,
      labels: <?= json_encode(array_column($ajuanPerPilar, 'nama_pilar')) ?>,
      colors: COLORS,
      legend: { show: false },
      dataLabels: { enabled: false },
      tooltip: { y: { formatter: function (v) { return v + ' ajuan'; } } },
    });

    // 1b. Horizontal bar Ajuan per Kecamatan
    render('#chartAjuanKec', {
      chart: { type: 'bar', height: 480, toolbar: { show: false } },
      plotOptions: { bar: { borderRadius: 4, horizontal: true } },
      dataLabels: { enabled: false },
      series: [{ name: 'Jumlah Ajuan', data: <?= json_encode(array_values(array_map(static fn ($r) => (int) $r['jumlah'], $ajuanPerKecamatan))) ?> }],
      xaxis: { categories: <?= json_encode(array_column($ajuanPerKecamatan, 'nama_kecamatan')) ?>, labels: { style: { colors: textMuted } } },
      yaxis: { labels: { style: { colors: textMuted } } },
      colors: ['#7367f0'],
      grid: { borderColor: gridColor },
    });

    // 2a. Bar Dana per Pilar
    render('#chartDanaPilar', {
      chart: { type: 'bar', height: 220, toolbar: { show: false } },
      plotOptions: { bar: { borderRadius: 5, distributed: true, columnWidth: '55%' } },
      dataLabels: { enabled: false },
      series: [{ name: 'Total Tersalurkan', data: <?= json_encode(array_values(array_map(static fn ($r) => (float) $r['total_tersalurkan'], $danaPerPilar))) ?> }],
      xaxis: { categories: <?= json_encode(array_column($danaPerPilar, 'nama_pilar')) ?>, labels: { style: { colors: textMuted } } },
      yaxis: { labels: { style: { colors: textMuted }, formatter: function (v) { return fmtRupiah(v); } } },
      colors: COLORS,
      legend: { show: false },
      tooltip: { y: { formatter: function (v) { return fmtRupiah(v); } } },
      grid: { borderColor: gridColor },
    });

    // 2b. Horizontal bar Dana per Kecamatan
    render('#chartDanaKec', {
      chart: { type: 'bar', height: 480, toolbar: { show: false } },
      plotOptions: { bar: { borderRadius: 4, horizontal: true } },
      dataLabels: { enabled: false },
      series: [{ name: 'Dana Tersalurkan', data: <?= json_encode(array_values(array_map(static fn ($r) => (float) $r['total_tersalurkan'], $danaPerKecamatan))) ?> }],
      xaxis: {
        categories: <?= json_encode(array_column($danaPerKecamatan, 'nama_kecamatan')) ?>,
        labels: { style: { colors: textMuted }, formatter: function (v) { return fmtRupiah(v); } },
      },
      yaxis: { labels: { style: { colors: textMuted } } },
      colors: ['#28c76f'],
      tooltip: { x: { formatter: function (v) { return fmtRupiah(v); } } },
      grid: { borderColor: gridColor },
    });

    // 2c. Pie Persentase Dana per Pilar
    render('#chartDanaPiePilar', {
      chart: { type: 'pie', height: 260 },
      series: <?= json_encode(array_values(array_map(static fn ($r) => (float) $r['total_tersalurkan'], $danaPerPilar))) ?>,
      labels: <?= json_encode(array_column($danaPerPilar, 'nama_pilar')) ?>,
      colors: COLORS,
      legend: { show: false },
      dataLabels: { enabled: false },
      tooltip: { y: { formatter: function (v) { return fmtRupiah(v); } } },
    });

    // 3a. Line Tren Ajuan per Pilar
    <?php
    $trenAjuanSeries = [];
    foreach ($pilarList as $nama) {
        $pts = [];
        foreach ($tahunAjuanList as $thn) {
            $pts[] = $trenAjuanMatrix[$nama][$thn] ?? 0;
        }
        $trenAjuanSeries[] = ['name' => $nama, 'data' => $pts];
    }
    ?>
    render('#chartTrenAjuan', {
      chart: { type: 'line', height: 280, toolbar: { show: false } },
      series: <?= json_encode($trenAjuanSeries) ?>,
      colors: COLORS,
      stroke: { curve: 'smooth', width: 2 },
      markers: { size: 4 },
      xaxis: { categories: <?= json_encode(array_values($tahunAjuanList)) ?>, labels: { style: { colors: textMuted } } },
      yaxis: { labels: { style: { colors: textMuted } } },
      legend: { position: 'bottom', labels: { colors: textMuted }, fontSize: '11px' },
      grid: { borderColor: gridColor },
    });

    // 3b. Line Tren Dana per Pilar
    <?php
    $trenDanaSeries = [];
    foreach ($pilarList as $nama) {
        $pts = [];
        foreach ($tahunDanaList as $thn) {
            $pts[] = $trenDanaMatrix[$nama][$thn] ?? 0;
        }
        $trenDanaSeries[] = ['name' => $nama, 'data' => $pts];
    }
    ?>
    render('#chartTrenDana', {
      chart: { type: 'line', height: 280, toolbar: { show: false } },
      series: <?= json_encode($trenDanaSeries) ?>,
      colors: COLORS,
      stroke: { curve: 'smooth', width: 2 },
      markers: { size: 4 },
      xaxis: { categories: <?= json_encode(array_values($tahunDanaList)) ?>, labels: { style: { colors: textMuted } } },
      yaxis: { labels: { style: { colors: textMuted }, formatter: function (v) { return fmtRupiah(v); } } },
      legend: { position: 'bottom', labels: { colors: textMuted }, fontSize: '11px' },
      tooltip: { y: { formatter: function (v) { return fmtRupiah(v); } } },
      grid: { borderColor: gridColor },
    });
  })();
</script>
<?= $this->endSection() ?>
