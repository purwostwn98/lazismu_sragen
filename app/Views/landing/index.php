<!doctype html>
<?php
$pilar         = $pilar ?? [];
$totalPilar    = $totalPilar ?? count($pilar);
$totalKategori = $totalKategori ?? 0;
$totalProgram  = $totalProgram ?? 0;
?>
<html lang="en" dir="ltr" data-skin="default" data-bs-theme="light" data-assets-path="<?= base_url('assets/') ?>">

<head>
  <meta charset="utf-8" />
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title><?= esc($title ?? 'Beranda') ?> | Lazismu Sragen</title>
  <meta name="description" content="Lazismu Sragen - Lembaga Amil Zakat, Infak, dan Sedekah Muhammadiyah Sragen" />

  <link rel="icon" type="image/png" href="<?= base_url('assets/img/logo/kembang.png') ?>" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet" />

  <link rel="stylesheet" href="<?= base_url('assets/vendor/fonts/iconify-icons.css') ?>" />
  <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/node-waves/node-waves.css') ?>" />
  <link rel="stylesheet" href="<?= base_url('assets/vendor/css/core.css') ?>" />
  <link rel="stylesheet" href="<?= base_url('assets/css/demo.css') ?>" />

  <script src="<?= base_url('assets/vendor/js/helpers.js') ?>"></script>
  <script src="<?= base_url('assets/vendor/js/template-customizer.js') ?>"></script>
  <script src="<?= base_url('assets/js/config.js') ?>"></script>

  <style>
    html,
    body {
      height: 100%;
    }

    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    body>main {
      flex: 1 0 auto;
    }

    body>footer {
      flex-shrink: 0;
    }

    /* ---------- Shared section styles ---------- */
    .lp-section-label {
      font-size: 0.78rem;
      font-weight: 700;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      color: var(--bs-primary);
      margin-bottom: 0.5rem;
    }

    .lp-section-title {
      font-size: clamp(1.6rem, 3vw, 2.2rem);
      font-weight: 800;
      color: #111827;
      letter-spacing: -0.02em;
      line-height: 1.25;
    }

    .lp-section-title span {
      color: var(--bs-primary);
    }

    .lp-section-sub {
      color: #6b7280;
      font-size: 1rem;
      max-width: 560px;
      margin: 0 auto;
      line-height: 1.75;
    }

    /* ---------- Navbar ---------- */
    .lp-navbar {
      position: sticky;
      top: 0;
      z-index: 1030;
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(15, 23, 42, 0.06);
    }

    .lp-nav-link {
      font-weight: 600;
      font-size: 0.92rem;
      color: #374151;
      text-decoration: none;
      padding: 0.5rem 0.9rem;
      border-radius: 8px;
      transition: color 0.2s, background 0.2s;
    }

    .lp-nav-link:hover {
      color: var(--bs-primary);
      background: rgba(var(--bs-primary-rgb), 0.08);
    }

    /* ---------- Hero ---------- */
    .lp-hero {
      position: relative;
      overflow: hidden;
      padding: 5.5rem 0 4rem;
      background: linear-gradient(180deg, rgba(var(--bs-primary-rgb), 0.06) 0%, rgba(255, 255, 255, 0) 60%);
    }

    .lp-hero-blob {
      position: absolute;
      border-radius: 50%;
      filter: blur(70px);
      opacity: 0.35;
      z-index: 0;
    }

    .lp-hero-blob.a {
      width: 420px;
      height: 420px;
      background: var(--bs-primary);
      top: -160px;
      right: -120px;
    }

    .lp-hero-blob.b {
      width: 320px;
      height: 320px;
      background: #22c55e;
      bottom: -140px;
      left: -100px;
      opacity: 0.18;
    }

    .lp-hero-content {
      position: relative;
      z-index: 1;
    }

    .lp-hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      background: #fff;
      border: 1px solid rgba(var(--bs-primary-rgb), 0.25);
      color: var(--bs-primary);
      font-weight: 700;
      font-size: 0.8rem;
      padding: 0.5rem 1rem;
      border-radius: 999px;
      box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
      margin-bottom: 1.5rem;
    }

    .lp-hero-title {
      font-size: clamp(2.1rem, 5vw, 3.4rem);
      font-weight: 800;
      letter-spacing: -0.03em;
      line-height: 1.15;
      color: #111827;
      margin-bottom: 1.25rem;
    }

    .lp-hero-title span {
      color: var(--bs-primary);
    }

    .lp-hero-desc {
      font-size: 1.05rem;
      color: #4b5563;
      max-width: 620px;
      margin: 0 auto 2.25rem;
      line-height: 1.8;
    }

    .lp-hero-actions {
      margin-bottom: 3rem;
    }

    .lp-hero-actions .btn {
      padding: 0.75rem 1.5rem;
      font-weight: 600;
      border-radius: 12px;
    }

    /* Stat strip, floated as a distinct card below the hero copy */
    .lp-stat-card {
      position: relative;
      z-index: 2;
      background: #fff;
      border-radius: 24px;
      box-shadow: 0 30px 60px -12px rgba(15, 23, 42, 0.18);
      padding: 2rem 1rem;
    }

    .lp-stat-item {
      text-align: center;
      padding: 0.75rem 1rem;
    }

    .lp-stat-value {
      font-size: clamp(1.8rem, 3vw, 2.4rem);
      font-weight: 800;
      color: var(--bs-primary);
      letter-spacing: -0.02em;
      line-height: 1;
    }

    .lp-stat-label {
      margin-top: 0.4rem;
      font-size: 0.85rem;
      font-weight: 600;
      color: #6b7280;
    }

    /* ---------- Feature (why us) cards ---------- */
    .lp-features {
      padding: 6.5rem 0 5rem;
    }

    .lp-feature-card {
      height: 100%;
      padding: 1.75rem;
      border-radius: 18px;
      border: 1px solid #eef0f2;
      background: #fff;
      transition: transform 0.3s, box-shadow 0.3s, border-color 0.3s;
    }

    .lp-feature-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
      border-color: transparent;
    }

    .lp-feature-icon {
      width: 52px;
      height: 52px;
      border-radius: 14px;
      background: rgba(var(--bs-primary-rgb), 0.12);
      color: var(--bs-primary);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.35rem;
      margin-bottom: 1.1rem;
    }

    .lp-feature-card h5 {
      font-weight: 700;
      font-size: 1.02rem;
      color: #111827;
      margin-bottom: 0.5rem;
    }

    .lp-feature-card p {
      font-size: 0.88rem;
      color: #6b7280;
      line-height: 1.7;
      margin-bottom: 0;
    }

    /* ---------- Programs (existing) ---------- */
    .lp-programs {
      background: #f8f9fa;
      padding: 5rem 0;
    }

    .lp-pilar-card {
      background: #fff;
      border-radius: 20px;
      padding: 2rem 1.75rem;
      height: 100%;
      position: relative;
      overflow: hidden;
      border: 1px solid #e5e7eb;
      cursor: pointer;
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .lp-pilar-card::before {
      content: '';
      position: absolute;
      inset: 0;
      border-radius: 20px;
      background: linear-gradient(135deg, var(--bs-primary), rgba(var(--bs-primary-rgb), 0.75));
      opacity: 0;
      transition: opacity 0.3s;
    }

    .lp-pilar-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 24px 48px rgba(var(--bs-primary-rgb), 0.18);
    }

    .lp-pilar-card:hover::before {
      opacity: 1;
    }

    .lp-pilar-card * {
      position: relative;
      z-index: 1;
    }

    .lp-pilar-icon {
      width: 60px;
      height: 60px;
      border-radius: 16px;
      background: rgba(var(--bs-primary-rgb), 0.12);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      color: var(--bs-primary);
      margin-bottom: 1.25rem;
      transition: background 0.3s, color 0.3s;
    }

    .lp-pilar-card:hover .lp-pilar-icon {
      background: rgba(255, 255, 255, 0.18);
      color: #fff;
    }

    .lp-pilar-card h4 {
      font-size: 1.1rem;
      font-weight: 700;
      color: #111827;
      margin-bottom: 0.6rem;
      transition: color 0.3s;
    }

    .lp-pilar-card:hover h4 {
      color: #fff;
    }

    .lp-pilar-card p {
      font-size: 0.88rem;
      color: #6b7280;
      line-height: 1.7;
      margin-bottom: 1rem;
      transition: color 0.3s;
    }

    .lp-pilar-card:hover p {
      color: rgba(255, 255, 255, 0.85);
    }

    .lp-pilar-meta {
      font-size: 0.78rem;
      font-weight: 700;
      color: var(--bs-primary);
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      transition: color 0.3s;
    }

    .lp-pilar-card:hover .lp-pilar-meta {
      color: #fbbf24;
    }

    .lp-syarat-list {
      margin-bottom: 0;
      padding-left: 1.1rem;
    }

    .lp-syarat-list li {
      font-size: 0.85rem;
      color: #6b7280;
      margin-bottom: 0.25rem;
    }

    /* ---------- Cara Pengajuan (steps) ---------- */
    .lp-steps {
      padding: 6.5rem 0;
    }

    .lp-step {
      position: relative;
      text-align: center;
      padding: 0 0.75rem;
    }

    .lp-step-circle {
      width: 64px;
      height: 64px;
      margin: 0 auto 1.1rem;
      border-radius: 50%;
      background: #fff;
      border: 2px solid rgba(var(--bs-primary-rgb), 0.25);
      color: var(--bs-primary);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.4rem;
      font-weight: 700;
      position: relative;
      z-index: 1;
    }

    .lp-step-num {
      position: absolute;
      top: -6px;
      right: -6px;
      width: 22px;
      height: 22px;
      border-radius: 50%;
      background: var(--bs-primary);
      color: #fff;
      font-size: 0.7rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .lp-step-line {
      display: none;
    }

    @media (min-width: 992px) {
      .lp-step-line {
        display: block;
        position: absolute;
        top: 32px;
        left: 50%;
        width: 100%;
        height: 2px;
        background: repeating-linear-gradient(90deg, rgba(var(--bs-primary-rgb), 0.3) 0 8px, transparent 8px 16px);
        z-index: 0;
      }
    }

    .lp-step:last-child .lp-step-line {
      display: none;
    }

    .lp-step h6 {
      font-weight: 700;
      font-size: 0.95rem;
      color: #111827;
      margin-bottom: 0.4rem;
    }

    .lp-step p {
      font-size: 0.84rem;
      color: #6b7280;
      line-height: 1.6;
      margin-bottom: 0;
    }

    /* ---------- CTA / Contact banner ---------- */
    .lp-cta {
      position: relative;
      overflow: hidden;
      border-radius: 28px;
      background: linear-gradient(135deg, var(--bs-primary), #b9750f);
      padding: 3rem 2.5rem;
      color: #fff;
      margin-bottom: 5rem;
    }

    .lp-cta::after {
      content: '';
      position: absolute;
      width: 280px;
      height: 280px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.12);
      right: -80px;
      bottom: -100px;
    }

    .lp-cta-content {
      position: relative;
      z-index: 1;
    }

    .lp-cta h3 {
      font-weight: 800;
      font-size: clamp(1.35rem, 2.5vw, 1.75rem);
      margin-bottom: 0.6rem;
      color: #fff;
    }

    .lp-cta p {
      opacity: 0.92;
      max-width: 520px;
      margin-bottom: 0;
      color: #fff;
    }

    .lp-cta-info {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      font-size: 0.88rem;
      margin-top: 0.9rem;
    }

    .lp-cta-info i {
      font-size: 1.05rem;
    }

    /* ---------- Footer ---------- */
    .lp-footer {
      background: #111827;
      color: rgba(255, 255, 255, 0.72);
      padding: 3.5rem 0 0;
    }

    .lp-footer h6 {
      color: #fff;
      font-weight: 700;
      font-size: 0.95rem;
      margin-bottom: 1rem;
    }

    .lp-footer-links a {
      color: rgba(255, 255, 255, 0.65);
      text-decoration: none;
      font-size: 0.88rem;
      display: block;
      margin-bottom: 0.6rem;
      transition: color 0.2s;
    }

    .lp-footer-links a:hover {
      color: #fff;
    }

    .lp-footer-contact {
      font-size: 0.85rem;
      line-height: 1.8;
    }

    .lp-footer-bottom {
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      margin-top: 2.5rem;
      padding: 1.25rem 0;
      text-align: center;
      font-size: 0.82rem;
      color: rgba(255, 255, 255, 0.5);
    }
  </style>
</head>

<body>
  <!-- Navbar -->
  <nav class="lp-navbar navbar navbar-expand-md py-3">
    <div class="container-xxl d-flex align-items-center justify-content-between">
      <a href="<?= base_url('/') ?>" class="d-flex align-items-center text-decoration-none">
        <img src="<?= base_url('assets/img/logo/logo.png') ?>" alt="Lazismu Sragen" style="height: 40px; width: auto;" />
      </a>
      <div class="d-none d-md-flex align-items-center gap-1">
        <a href="#beranda" class="lp-nav-link">Beranda</a>
        <a href="#program" class="lp-nav-link">Kegiatan</a>
        <a href="#cara-pengajuan" class="lp-nav-link">Cara Pengajuan</a>
        <a href="#kontak" class="lp-nav-link">Kontak</a>
      </div>
      <div class="d-flex align-items-center gap-2">
        <a href="<?= base_url('pengajuan') ?>" class="btn btn-label-primary d-none d-sm-inline-flex">Ajukan Bantuan</a>
        <a href="<?= base_url('login') ?>" class="btn btn-primary">
          <i class="icon-base ti tabler-login me-1"></i>Login
        </a>
      </div>
    </div>
  </nav>
  <!-- / Navbar -->

  <main>
    <!-- Hero -->
    <section id="beranda" class="lp-hero">
      <div class="lp-hero-blob a"></div>
      <div class="lp-hero-blob b"></div>
      <div class="container-xxl text-center lp-hero-content">
        <div class="mb-3">
          <img
            src="<?= base_url('assets/img/logo/logo_simpul.png') ?>"
            alt="SIMPUL Sragen"
            style="height: 110px; width: auto;" />
          <div style="font-size: 1.25rem; color: #6b7280; font-weight: 600;">
            Sistem Mandiri Pengajuan Umat Lazismu
          </div>
        </div>
        <span class="lp-hero-badge">
          <i class="icon-base ti tabler-certificate"></i>
          Lembaga Amil Zakat Resmi Muhammadiyah
        </span>
        <h1 class="lp-hero-title">
          Salurkan Kepedulian, <span>Wujudkan Kebermanfaatan</span>
        </h1>
        <p class="lp-hero-desc">
          Lazismu Sragen menghimpun dan menyalurkan zakat, infak, dan sedekah secara amanah dan transparan melalui
          program pendidikan, kesehatan, ekonomi, dakwah, sosial, dan kemanusiaan bagi masyarakat Sragen.
        </p>
        <div class="lp-hero-actions d-flex flex-wrap justify-content-center gap-2">
          <a href="<?= base_url('pengajuan') ?>" class="btn btn-primary">
            <i class="icon-base ti tabler-heart-handshake me-1"></i> Ajukan Bantuan
          </a>
          <a href="#program" class="btn btn-outline-primary">Lihat Kegiatan</a>
          <a href="<?= base_url('pengajuan/status') ?>" class="btn btn-label-secondary">Cek Status Ajuan</a>
        </div>
      </div>

      <div class="container-xxl">
        <div class="lp-stat-card">
          <div class="row g-3">
            <div class="col-4 lp-stat-item">
              <div class="lp-stat-value"><?= (int) $totalPilar ?></div>
              <div class="lp-stat-label">Pilar Program</div>
            </div>
            <div class="col-4 lp-stat-item">
              <div class="lp-stat-value"><?= (int) $totalKategori ?></div>
              <div class="lp-stat-label">Kategori Program</div>
            </div>
            <div class="col-4 lp-stat-item">
              <div class="lp-stat-value"><?= (int) $totalProgram ?>+</div>
              <div class="lp-stat-label">Kegiatan Bantuan</div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- / Hero -->

    <!-- Why us -->
    <section class="lp-features">
      <div class="container-xxl">
        <div class="text-center mb-6">
          <p class="lp-section-label">Kenapa Lazismu Sragen</p>
          <h2 class="lp-section-title">Amanah Mengelola, <span>Tepat Menyalurkan</span></h2>
          <p class="lp-section-sub mt-3 mx-auto">
            Setiap zakat, infak, dan sedekah yang Anda titipkan dikelola dengan prinsip yang jelas dan dapat
            dipertanggungjawabkan.
          </p>
        </div>
        <div class="row g-4">
          <div class="col-sm-6 col-lg-3">
            <div class="lp-feature-card">
              <div class="lp-feature-icon"><i class="icon-base ti tabler-shield-check"></i></div>
              <h5>Amanah &amp; Transparan</h5>
              <p>Pengelolaan dana dicatat secara tertib dan dapat dipertanggungjawabkan kepada muzaki maupun publik.</p>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="lp-feature-card">
              <div class="lp-feature-icon"><i class="icon-base ti tabler-target-arrow"></i></div>
              <h5>Tepat Sasaran</h5>
              <p>Setiap ajuan bantuan diverifikasi sebelum disalurkan agar benar-benar sampai kepada yang berhak.</p>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="lp-feature-card">
              <div class="lp-feature-icon"><i class="icon-base ti tabler-devices"></i></div>
              <h5>Mudah &amp; Cepat</h5>
              <p>Pengajuan bantuan dapat dilakukan kapan saja secara online, tanpa perlu datang langsung ke kantor.</p>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="lp-feature-card">
              <div class="lp-feature-icon"><i class="icon-base ti tabler-building-mosque"></i></div>
              <h5>Resmi &amp; Terstruktur</h5>
              <p>Bagian dari jaringan Lazismu Muhammadiyah yang bergerak di bidang pendidikan, kesehatan, dan sosial.</p>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- / Why us -->

    <!-- Programs -->
    <section id="program" class="lp-programs">
      <div class="container-xxl">
        <div class="text-center mb-6">
          <p class="lp-section-label">Pilar Program</p>
          <h2 class="lp-section-title">Kegiatan <span>Kami</span></h2>
          <p class="lp-section-sub mt-3 mx-auto">
            Klik salah satu pilar untuk melihat kategori, kegiatan, dan syarat pengajuan di dalamnya.
          </p>
        </div>

        <div class="row g-6">
          <?php if (empty($pilar)): ?>
            <div class="col-12 text-center text-body-secondary py-6">Belum ada pilar yang ditampilkan.</div>
          <?php endif; ?>
          <?php
          $pilarIcons = [
            'tabler-book',
            'tabler-heart-handshake',
            'tabler-chart-line',
            'tabler-building-mosque',
            'tabler-lifebuoy',
            'tabler-gift',
          ];
          ?>
          <?php foreach ($pilar as $i => $pl): ?>
            <?php
            $jumlahKategori = count($pl['kategori']);
            $jumlahProgram  = array_sum(array_map(static fn($k) => count($k['program']), $pl['kategori']));
            ?>
            <div class="col-sm-6 col-lg-4">
              <div class="lp-pilar-card" data-bs-toggle="modal" data-bs-target="#modalPilar<?= $pl['id_pilar'] ?>">
                <div class="lp-pilar-icon">
                  <i class="icon-base ti <?= $pilarIcons[$i % count($pilarIcons)] ?>"></i>
                </div>
                <h4><?= esc($pl['nama_pilar']) ?></h4>
                <p><?= esc($pl['deskripsi_pilar']) ?></p>
                <span class="lp-pilar-meta">
                  <?= $jumlahKategori ?> Kategori &middot; <?= $jumlahProgram ?> Kegiatan
                  <i class="icon-base ti tabler-arrow-right"></i>
                </span>
              </div>
            </div>

            <!-- Detail modal: kategori > program > syarat pengajuan -->
            <div class="modal fade" id="modalPilar<?= $pl['id_pilar'] ?>" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title"><?= esc($pl['nama_pilar']) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <p class="text-body-secondary"><?= esc($pl['deskripsi_pilar']) ?></p>

                    <?php if (empty($pl['kategori'])): ?>
                      <p class="text-body-secondary mb-0">Belum ada kategori untuk pilar ini.</p>
                    <?php else: ?>
                      <div class="accordion" id="accordionPilar<?= $pl['id_pilar'] ?>">
                        <?php foreach ($pl['kategori'] as $k): ?>
                          <?php $katId = $pl['id_pilar'] . '_' . $k['id_kategori_program']; ?>
                          <div class="accordion-item">
                            <h2 class="accordion-header">
                              <button
                                class="accordion-button collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapse<?= $katId ?>">
                                <?= esc($k['nama_kategori']) ?>
                                <span class="badge bg-label-primary ms-2"><?= count($k['program']) ?> Kegiatan</span>
                              </button>
                            </h2>
                            <div
                              id="collapse<?= $katId ?>"
                              class="accordion-collapse collapse"
                              data-bs-parent="#accordionPilar<?= $pl['id_pilar'] ?>">
                              <div class="accordion-body">
                                <p class="text-body-secondary"><?= esc($k['deskripsi_kategori']) ?></p>

                                <?php if (empty($k['program'])): ?>
                                  <p class="text-body-secondary mb-0">Belum ada kegiatan pada kategori ini.</p>
                                <?php endif; ?>

                                <?php foreach ($k['program'] as $prog): ?>
                                  <div class="border rounded-2 p-3 mb-3">
                                    <h6 class="mb-1"><?= esc($prog['nama_program']) ?></h6>
                                    <?php if (!empty($prog['deskripsi_program'])): ?>
                                      <p class="text-body-secondary mb-2"><?= esc($prog['deskripsi_program']) ?></p>
                                    <?php endif; ?>

                                    <div class="fw-medium small mb-1">Syarat Pengajuan:</div>
                                    <?php if (empty($prog['syarat'])): ?>
                                      <p class="text-body-secondary small mb-0">Belum ada syarat yang ditentukan.</p>
                                    <?php else: ?>
                                      <ul class="lp-syarat-list">
                                        <?php foreach ($prog['syarat'] as $sy): ?>
                                          <li><?= esc($sy['syarat_program']) ?></li>
                                        <?php endforeach; ?>
                                      </ul>
                                    <?php endif; ?>
                                  </div>
                                <?php endforeach; ?>
                              </div>
                            </div>
                          </div>
                        <?php endforeach; ?>
                      </div>
                    <?php endif; ?>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a href="<?= base_url('pengajuan') ?>" class="btn btn-primary">Ajukan Bantuan</a>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <!-- / Programs -->

    <!-- Cara Pengajuan -->
    <section id="cara-pengajuan" class="lp-steps">
      <div class="container-xxl">
        <div class="text-center mb-6">
          <p class="lp-section-label">Alur Pengajuan</p>
          <h2 class="lp-section-title">Cara <span>Mengajukan Bantuan</span></h2>
          <p class="lp-section-sub mt-3 mx-auto">
            Empat langkah sederhana, sepenuhnya online, tanpa perlu datang ke kantor.
          </p>
        </div>

        <div class="row g-5">
          <div class="col-sm-6 col-lg-3 lp-step">
            <div class="lp-step-line"></div>
            <div class="lp-step-circle">
              <span class="lp-step-num">1</span>
              <i class="icon-base ti tabler-id"></i>
            </div>
            <h6>Cek NIK</h6>
            <p>Masukkan NIK untuk memeriksa apakah Anda sudah pernah terdaftar sebagai pemohon.</p>
          </div>
          <div class="col-sm-6 col-lg-3 lp-step">
            <div class="lp-step-line"></div>
            <div class="lp-step-circle">
              <span class="lp-step-num">2</span>
              <i class="icon-base ti tabler-forms"></i>
            </div>
            <h6>Lengkapi Biodata</h6>
            <p>Pemohon baru mengisi biodata diri dan alamat domisili terlebih dahulu.</p>
          </div>
          <div class="col-sm-6 col-lg-3 lp-step">
            <div class="lp-step-line"></div>
            <div class="lp-step-circle">
              <span class="lp-step-num">3</span>
              <i class="icon-base ti tabler-clipboard-check"></i>
            </div>
            <h6>Isi Formulir Ajuan</h6>
            <p>Pilih kegiatan bantuan, lengkapi data mustahik/lembaga, dan unggah berkas persyaratan.</p>
          </div>
          <div class="col-sm-6 col-lg-3 lp-step">
            <div class="lp-step-circle">
              <span class="lp-step-num">4</span>
              <i class="icon-base ti tabler-ticket"></i>
            </div>
            <h6>Dapat Nomor Ajuan</h6>
            <p>Simpan nomor ajuan untuk mencetak bukti dan memantau status pengajuan Anda.</p>
          </div>
        </div>

        <div class="text-center mt-6">
          <a href="<?= base_url('pengajuan') ?>" class="btn btn-primary">
            Mulai Pengajuan <i class="icon-base ti tabler-arrow-right ms-1"></i>
          </a>
        </div>
      </div>
    </section>
    <!-- / Cara Pengajuan -->

    <!-- CTA / Kontak -->
    <section id="kontak" class="container-xxl">
      <div class="lp-cta">
        <div class="row align-items-center g-4">
          <div class="col-lg-8 lp-cta-content">
            <h3>Ada Pertanyaan atau Ingin Berdonasi?</h3>
            <p>Hubungi kantor Lazismu Sragen langsung untuk informasi kegiatan, kerja sama, maupun penyaluran donasi.</p>
            <div class="lp-cta-info">
              <i class="icon-base ti tabler-map-pin"></i>
              <span>Widoro, RT.37/RW.11, Dusun Kebayanan Widodo 1, Sragen Wetan, Kec. Sragen, Kabupaten Sragen, Jawa Tengah 57214</span>
            </div>
            <div class="lp-cta-info">
              <i class="icon-base ti tabler-phone"></i>
              <span>0851-0000-0098</span>
            </div>
          </div>
          <div class="col-lg-4 lp-cta-content text-lg-end">
            <a href="tel:+6285100000098" class="btn btn-light fw-semibold">
              <i class="icon-base ti tabler-phone me-1"></i> Hubungi Kami
            </a>
          </div>
        </div>
      </div>
    </section>
    <!-- / CTA / Kontak -->
  </main>

  <!-- Footer -->
  <footer class="lp-footer">
    <div class="container-xxl">
      <div class="row g-4">
        <div class="col-lg-4">
          <div class="d-inline-block bg-white rounded-2 px-3 py-2 mb-3">
            <img src="<?= base_url('assets/img/logo/logo.png') ?>" alt="Lazismu Sragen" style="height: 32px; width: auto;" />
          </div>
          <p class="lp-footer-contact mb-0">
            Lembaga Amil Zakat, Infak, dan Sedekah Muhammadiyah Sragen &mdash; menyalurkan kepedulian menjadi
            kebermanfaatan bagi masyarakat.
          </p>
        </div>
        <div class="col-6 col-lg-2">
          <h6>Tautan</h6>
          <div class="lp-footer-links">
            <a href="#beranda">Beranda</a>
            <a href="#program">Kegiatan</a>
            <a href="#cara-pengajuan">Cara Pengajuan</a>
          </div>
        </div>
        <div class="col-6 col-lg-2">
          <h6>Layanan</h6>
          <div class="lp-footer-links">
            <a href="<?= base_url('pengajuan') ?>">Ajukan Bantuan</a>
            <a href="<?= base_url('pengajuan/status') ?>">Cek Status Ajuan</a>
            <a href="<?= base_url('login') ?>">Masuk Admin</a>
          </div>
        </div>
        <div class="col-lg-4">
          <h6>Kontak</h6>
          <div class="lp-footer-contact">
            <div class="d-flex gap-2 mb-2">
              <i class="icon-base ti tabler-map-pin mt-1"></i>
              <span>Widoro, RT.37/RW.11, Dusun Kebayanan Widodo 1, Sragen Wetan, Kec. Sragen, Kabupaten Sragen, Jawa Tengah 57214</span>
            </div>
            <div class="d-flex gap-2">
              <i class="icon-base ti tabler-phone mt-1"></i>
              <span>0851-0000-0098</span>
            </div>
          </div>
        </div>
      </div>

      <div class="lp-footer-bottom">
        &#169; <?= date('Y') ?>, Lazismu Sragen. Seluruh hak cipta dilindungi.
      </div>
    </div>
  </footer>
  <!-- / Footer -->

  <script src="<?= base_url('assets/vendor/libs/jquery/jquery.js') ?>"></script>
  <script src="<?= base_url('assets/vendor/libs/popper/popper.js') ?>"></script>
  <script src="<?= base_url('assets/vendor/js/bootstrap.js') ?>"></script>
  <script src="<?= base_url('assets/vendor/libs/node-waves/node-waves.js') ?>"></script>
  <script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>

</html>