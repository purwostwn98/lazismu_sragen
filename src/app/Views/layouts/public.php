<!doctype html>

<html lang="en" dir="ltr" data-skin="default" data-bs-theme="light" data-assets-path="<?= base_url('assets/') ?>">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title><?= esc($title ?? 'Lazismu Sragen') ?> | Lazismu Sragen</title>

    <link rel="icon" type="image/png" href="<?= base_url('assets/img/logo/kembang.png') ?>" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="<?= base_url('assets/vendor/fonts/iconify-icons.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/node-waves/node-waves.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/vendor/css/core.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/css/demo.css') ?>" />

    <?= $this->renderSection('pageStyles') ?>

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

      body > .container-xxl.py-6 {
        flex: 1 0 auto;
      }

      body > footer {
        flex-shrink: 0;
      }
    </style>

    <script src="<?= base_url('assets/vendor/js/helpers.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/js/template-customizer.js') ?>"></script>
    <script src="<?= base_url('assets/js/config.js') ?>"></script>
  </head>

  <body>
    <!-- Navbar -->
    <nav class="layout-navbar container-xxl navbar navbar-expand-xl align-items-center bg-navbar-theme py-3">
      <div class="container-xxl d-flex align-items-center justify-content-between">
        <a href="<?= base_url('/') ?>" class="d-flex align-items-center text-decoration-none">
          <img src="<?= base_url('assets/img/logo/logo.png') ?>" alt="Lazismu Sragen" style="height: 44px; width: auto;" />
        </a>
        <a href="<?= base_url('/') ?>" class="btn btn-label-secondary btn-sm">
          <i class="icon-base ti tabler-arrow-left me-1"></i>Kembali ke Beranda
        </a>
      </div>
    </nav>
    <!-- / Navbar -->

    <div class="container-xxl py-6">
      <?= $this->renderSection('content') ?>
    </div>

    <!-- Footer -->
    <footer class="content-footer footer bg-footer-theme">
      <div class="container-xxl">
        <div class="footer-container d-flex align-items-center justify-content-center py-4 text-center">
          <div class="text-body">&#169; <?= date('Y') ?>, Lazismu Sragen. Seluruh hak cipta dilindungi.</div>
        </div>
      </div>
    </footer>
    <!-- / Footer -->

    <script src="<?= base_url('assets/vendor/libs/jquery/jquery.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/libs/popper/popper.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/js/bootstrap.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/libs/node-waves/node-waves.js') ?>"></script>
    <script src="<?= base_url('assets/js/main.js') ?>"></script>

    <?= $this->renderSection('pageScripts') ?>
  </body>
</html>
