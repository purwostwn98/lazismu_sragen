<!doctype html>

<html
  lang="en"
  class="layout-navbar-fixed layout-menu-fixed layout-compact"
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

  <title><?= esc($title ?? 'Dashboard') ?> | Lazismu Sragen</title>

  <meta name="description" content="Lazismu Sragen Admin" />

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="<?= base_url('assets/img/logo/kembang.png') ?>" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet" />

  <link rel="stylesheet" href="<?= base_url('assets/vendor/fonts/iconify-icons.css') ?>" />

  <script src="<?= base_url('assets/vendor/libs/@algolia/autocomplete-js.js') ?>"></script>

  <!-- Core CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/node-waves/node-waves.css') ?>" />
  <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/pickr/pickr-themes.css') ?>" />
  <link rel="stylesheet" href="<?= base_url('assets/vendor/css/core.css') ?>" />
  <link rel="stylesheet" href="<?= base_url('assets/css/demo.css') ?>" />

  <!-- Vendors CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') ?>" />
  <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') ?>" />

  <!-- Page CSS -->
  <?= $this->renderSection('pageStyles') ?>

  <!-- Helpers -->
  <script src="<?= base_url('assets/vendor/js/helpers.js') ?>"></script>
  <script src="<?= base_url('assets/vendor/js/template-customizer.js') ?>"></script>
  <script src="<?= base_url('assets/js/config.js') ?>"></script>
</head>

<body>
  <!-- Layout wrapper -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <!-- Menu -->
      <aside id="layout-menu" class="layout-menu menu-vertical menu">
        <div class="app-brand demo">
          <a href="<?= base_url('dashboard') ?>" class="app-brand-link d-flex flex-grow-1 justify-content-center">
            <img
              src="<?= base_url('assets/img/logo/logo.png') ?>"
              alt="Lazismu Sragen"
              class="app-brand-logo demo"
              style="height: 60px; width: auto;" />
          </a>

          <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="icon-base ti menu-toggle-icon d-none d-xl-block"></i>
            <i class="icon-base ti tabler-x d-block d-xl-none"></i>
          </a>
        </div>

        <div class="menu-inner-shadow"></div>

        <ul class="menu-inner py-1">
          <li class="menu-item <?= ($activeMenu ?? '') === 'dashboard' ? 'active' : '' ?>">
            <a href="<?= base_url('dashboard') ?>" class="menu-link">
              <i class="menu-icon icon-base ti tabler-smart-home"></i>
              <div data-i18n="Dashboard">Dashboard</div>
            </a>
          </li>
          <li class="menu-item <?= ($activeMenu ?? '') === 'analitik' ? 'active' : '' ?>">
            <a href="<?= base_url('dashboard/analitik') ?>" class="menu-link">
              <i class="menu-icon icon-base ti tabler-chart-bar"></i>
              <div data-i18n="Dashboard Analitik">Dashboard Analitik</div>
            </a>
          </li>

          <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Transaksi</span>
          </li>
          <?php $ajuanSubActive = in_array($activeMenu ?? '', ['ajuan', 'ajuan-individu', 'ajuan-lembaga', 'ajuan-rutin'], true); ?>
          <li class="menu-item <?= $ajuanSubActive ? 'active open' : '' ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
              <i class="menu-icon icon-base ti tabler-file-description"></i>
              <div data-i18n="Ajuan">Ajuan</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item <?= ($activeMenu ?? '') === 'ajuan-individu' ? 'active' : '' ?>">
                <a href="<?= base_url('ajuan/individu') ?>" class="menu-link">
                  <div data-i18n="Ajuan Individu">Ajuan Individu</div>
                </a>
              </li>
              <li class="menu-item <?= ($activeMenu ?? '') === 'ajuan-lembaga' ? 'active' : '' ?>">
                <a href="<?= base_url('ajuan/lembaga') ?>" class="menu-link">
                  <div data-i18n="Ajuan Lembaga">Ajuan Lembaga</div>
                </a>
              </li>
              <li class="menu-item <?= ($activeMenu ?? '') === 'ajuan-rutin' ? 'active' : '' ?>">
                <a href="<?= base_url('ajuan/rutin') ?>" class="menu-link">
                  <div data-i18n="Ajuan Rutin">Ajuan Rutin</div>
                </a>
              </li>
            </ul>
          </li>

          <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Data Master</span>
          </li>
          <?php $programSubActive = in_array($activeMenu ?? '', ['pilar', 'kategori-program', 'program'], true); ?>
          <li class="menu-item <?= $programSubActive ? 'active open' : '' ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
              <i class="menu-icon icon-base ti tabler-list-details"></i>
              <div data-i18n="Programs">Programs</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item <?= ($activeMenu ?? '') === 'pilar' ? 'active' : '' ?>">
                <a href="<?= base_url('pilar') ?>" class="menu-link">
                  <div data-i18n="Pilar">Pilar</div>
                </a>
              </li>
              <li class="menu-item <?= ($activeMenu ?? '') === 'kategori-program' ? 'active' : '' ?>">
                <a href="<?= base_url('kategori-program') ?>" class="menu-link">
                  <div data-i18n="Kategori Program">Kategori Program</div>
                </a>
              </li>
              <li class="menu-item <?= ($activeMenu ?? '') === 'program' ? 'active' : '' ?>">
                <a href="<?= base_url('program') ?>" class="menu-link">
                  <div data-i18n="Kegiatan">Kegiatan</div>
                </a>
              </li>
            </ul>
          </li>
          <?php $organisasiSubActive = in_array($activeMenu ?? '', ['jabatan', 'penjabat'], true); ?>
          <li class="menu-item <?= $organisasiSubActive ? 'active open' : '' ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
              <i class="menu-icon icon-base ti tabler-building-community"></i>
              <div data-i18n="Organisasi">Organisasi</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item <?= ($activeMenu ?? '') === 'jabatan' ? 'active' : '' ?>">
                <a href="<?= base_url('jabatan') ?>" class="menu-link">
                  <div data-i18n="Jabatan">Jabatan</div>
                </a>
              </li>
              <li class="menu-item <?= ($activeMenu ?? '') === 'penjabat' ? 'active' : '' ?>">
                <a href="<?= base_url('penjabat') ?>" class="menu-link">
                  <div data-i18n="Penjabat">Penjabat</div>
                </a>
              </li>
            </ul>
          </li>
          <li class="menu-item <?= ($activeMenu ?? '') === 'pemohon' ? 'active' : '' ?>">
            <a href="<?= base_url('pemohon') ?>" class="menu-link">
              <i class="menu-icon icon-base ti tabler-id-badge-2"></i>
              <div data-i18n="Pemohon">Pemohon</div>
            </a>
          </li>
          <?php $mustahikSubActive = in_array($activeMenu ?? '', ['mustahik-individu', 'mustahik-lembaga'], true); ?>
          <li class="menu-item <?= $mustahikSubActive ? 'active open' : '' ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
              <i class="menu-icon icon-base ti tabler-users-group"></i>
              <div data-i18n="Mustahik">Mustahik</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item <?= ($activeMenu ?? '') === 'mustahik-individu' ? 'active' : '' ?>">
                <a href="<?= base_url('mustahik/individu') ?>" class="menu-link">
                  <div data-i18n="Mustahik Individu">Mustahik Individu</div>
                </a>
              </li>
              <li class="menu-item <?= ($activeMenu ?? '') === 'mustahik-lembaga' ? 'active' : '' ?>">
                <a href="<?= base_url('mustahik/lembaga') ?>" class="menu-link">
                  <div data-i18n="Mustahik Lembaga">Mustahik Lembaga</div>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </aside>

      <div class="menu-mobile-toggler d-xl-none rounded-1">
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large text-bg-secondary p-2 rounded-1">
          <i class="ti tabler-menu icon-base"></i>
          <i class="ti tabler-chevron-right icon-base"></i>
        </a>
      </div>
      <!-- / Menu -->

      <!-- Layout container -->
      <div class="layout-page">
        <!-- Navbar -->
        <nav
          class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme"
          id="layout-navbar">
          <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
            <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
              <i class="icon-base ti tabler-menu-2 icon-md"></i>
            </a>
          </div>

          <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
            <div class="navbar-nav align-items-center">
              <div class="nav-item dropdown me-2 me-xl-0">
                <a
                  class="nav-link dropdown-toggle hide-arrow"
                  id="nav-theme"
                  href="javascript:void(0);"
                  data-bs-toggle="dropdown">
                  <i class="icon-base ti tabler-sun icon-md theme-icon-active"></i>
                  <span class="d-none ms-2" id="nav-theme-text">Toggle theme</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="nav-theme-text">
                  <li>
                    <button
                      type="button"
                      class="dropdown-item align-items-center active"
                      data-bs-theme-value="light"
                      aria-pressed="false">
                      <span><i class="icon-base ti tabler-sun icon-md me-3" data-icon="sun"></i>Light</span>
                    </button>
                  </li>
                  <li>
                    <button
                      type="button"
                      class="dropdown-item align-items-center"
                      data-bs-theme-value="dark"
                      aria-pressed="true">
                      <span><i class="icon-base ti tabler-moon-stars icon-md me-3" data-icon="moon-stars"></i>Dark</span>
                    </button>
                  </li>
                  <li>
                    <button
                      type="button"
                      class="dropdown-item align-items-center"
                      data-bs-theme-value="system"
                      aria-pressed="false">
                      <span><i
                          class="icon-base ti tabler-device-desktop-analytics icon-md me-3"
                          data-icon="device-desktop-analytics"></i>System</span>
                    </button>
                  </li>
                </ul>
              </div>
            </div>

            <ul class="navbar-nav flex-row align-items-center ms-md-auto">
              <!-- User -->
              <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a
                  class="nav-link dropdown-toggle hide-arrow p-0"
                  href="javascript:void(0);"
                  data-bs-toggle="dropdown">
                  <div class="avatar avatar-online">
                    <img src="<?= base_url('assets/img/avatars/1.png') ?>" alt class="rounded-circle" />
                  </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <a class="dropdown-item" href="#">
                      <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                          <div class="avatar avatar-online">
                            <img
                              src="<?= base_url('assets/img/avatars/1.png') ?>"
                              alt
                              class="w-px-40 h-auto rounded-circle" />
                          </div>
                        </div>
                        <div class="flex-grow-1">
                          <h6 class="mb-0"><?= esc(session('nama') ?? 'Admin') ?></h6>
                          <small class="text-body-secondary">Administrator</small>
                        </div>
                      </div>
                    </a>
                  </li>
                  <li>
                    <div class="dropdown-divider my-1 mx-n2"></div>
                  </li>
                  <li>
                    <a class="dropdown-item" href="#">
                      <i class="icon-base ti tabler-user icon-md me-3"></i><span>My Profile</span>
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" href="#">
                      <i class="icon-base ti tabler-settings icon-md me-3"></i><span>Settings</span>
                    </a>
                  </li>
                  <li>
                    <div class="dropdown-divider my-1 mx-n2"></div>
                  </li>
                  <li>
                    <a class="dropdown-item" href="<?= base_url('logout') ?>">
                      <i class="icon-base ti tabler-power icon-md me-3"></i><span>Log Out</span>
                    </a>
                  </li>
                </ul>
              </li>
              <!--/ User -->
            </ul>
          </div>
        </nav>
        <!-- / Navbar -->

        <!-- Content wrapper -->
        <div class="content-wrapper">
          <!-- Content -->
          <div class="container-xxl flex-grow-1 container-p-y">
            <?= $this->renderSection('content') ?>
          </div>
          <!-- / Content -->

          <!-- Footer -->
          <footer class="content-footer footer bg-footer-theme">
            <div class="container-xxl">
              <div
                class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
                <div class="text-body">
                  &#169; <?= date('Y') ?>, made with <span class="text-danger">&hearts;</span> by Lazismu Sragen
                </div>
              </div>
            </div>
          </footer>
          <!-- / Footer -->

          <div class="content-backdrop fade"></div>
        </div>
        <!-- Content wrapper -->
      </div>
      <!-- / Layout page -->
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>

    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>
  </div>
  <!-- / Layout wrapper -->

  <!-- Core JS -->
  <script src="<?= base_url('assets/vendor/libs/jquery/jquery.js') ?>"></script>
  <script src="<?= base_url('assets/vendor/libs/popper/popper.js') ?>"></script>
  <script src="<?= base_url('assets/vendor/js/bootstrap.js') ?>"></script>
  <script src="<?= base_url('assets/vendor/libs/node-waves/node-waves.js') ?>"></script>
  <script src="<?= base_url('assets/vendor/libs/pickr/pickr.js') ?>"></script>
  <script src="<?= base_url('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') ?>"></script>
  <script src="<?= base_url('assets/vendor/libs/hammer/hammer.js') ?>"></script>
  <script src="<?= base_url('assets/vendor/js/menu.js') ?>"></script>
  <script src="<?= base_url('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') ?>"></script>

  <!-- Vendors JS -->
  <?= $this->renderSection('pageScriptsVendor') ?>

  <!-- Main JS -->
  <script src="<?= base_url('assets/js/main.js') ?>"></script>
  <script src="<?= base_url('assets/js/datatable-init.js') ?>"></script>

  <!-- Page JS -->
  <?= $this->renderSection('pageScripts') ?>
</body>

</html>