<!doctype html>

<html
  lang="en"
  class="layout-wide"
  dir="ltr"
  data-skin="default"
  data-bs-theme="light"
  data-assets-path="<?= base_url('assets/') ?>"
  data-template="vertical-menu-template-starter">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title><?= esc($title ?? 'Login') ?> | Lazismu Sragen</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/logo/kembang.png') ?>" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="<?= base_url('assets/vendor/fonts/iconify-icons.css') ?>" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/node-waves/node-waves.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/vendor/css/core.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/css/demo.css') ?>" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') ?>" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/vendor/css/pages/page-auth.css') ?>" />

    <!-- Helpers -->
    <script src="<?= base_url('assets/vendor/js/helpers.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/js/template-customizer.js') ?>"></script>
    <script src="<?= base_url('assets/js/config.js') ?>"></script>
  </head>

  <body>
    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-6">
          <div class="card">
            <div class="card-body">
              <!-- Logo -->
              <div class="app-brand justify-content-center mb-6">
                <a href="<?= base_url('/') ?>" class="app-brand-link">
                  <img
                    src="<?= base_url('assets/img/logo/logo.png') ?>"
                    alt="Lazismu Sragen"
                    style="height: 60px; width: auto;" />
                </a>
              </div>
              <!-- /Logo -->

              <h4 class="mb-1">Selamat Datang 👋</h4>
              <p class="mb-6">Silakan masuk ke akun Anda untuk melanjutkan</p>

              <?php if (!empty($errorUser)) : ?>
                <div class="alert alert-danger" role="alert"><?= esc($errorUser) ?></div>
              <?php endif; ?>
              <?php if (!empty($errorPassword)) : ?>
                <div class="alert alert-danger" role="alert"><?= esc($errorPassword) ?></div>
              <?php endif; ?>
              <?php if (!empty($errorHitung)) : ?>
                <div class="alert alert-danger" role="alert"><?= esc($errorHitung) ?></div>
              <?php endif; ?>

              <form id="formAuthentication" class="mb-4" action="<?= base_url('login/attempt') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-6">
                  <label for="username" class="form-label">Username</label>
                  <input
                    type="text"
                    class="form-control"
                    id="username"
                    name="username"
                    value="<?= esc(old('username')) ?>"
                    placeholder="Masukkan username Anda"
                    autofocus
                    required />
                </div>
                <div class="mb-6 form-password-toggle">
                  <label class="form-label" for="password">Password</label>
                  <div class="input-group input-group-merge">
                    <input
                      type="password"
                      id="password"
                      class="form-control"
                      name="password"
                      placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                      aria-describedby="password"
                      required />
                    <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                  </div>
                </div>
                <div class="mb-6">
                  <label for="jawabCpt" class="form-label"><?= esc($captchaText) ?></label>
                  <input type="hidden" name="hslbenar" value="<?= esc($captchaHash) ?>" />
                  <input
                    type="text"
                    class="form-control"
                    id="jawabCpt"
                    name="jawabCpt"
                    placeholder="Jawaban dalam angka"
                    required />
                </div>
                <div class="mb-6">
                  <button class="btn btn-primary d-grid w-100" type="submit">Login</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Core JS -->
    <script src="<?= base_url('assets/vendor/libs/jquery/jquery.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/libs/popper/popper.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/js/bootstrap.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/libs/node-waves/node-waves.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') ?>"></script>

    <!-- Main JS -->
    <script src="<?= base_url('assets/js/main.js') ?>"></script>
  </body>
</html>
