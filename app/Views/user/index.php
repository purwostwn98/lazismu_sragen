<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<?php $iduserSaatIni = (int) session()->get('iduser'); ?>

<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <h5 class="mb-0">Manajemen User</h5>
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
      <i class="icon-base ti tabler-plus me-1"></i>Tambah User
    </button>
  </div>
  <div class="table-responsive">
    <table class="table table-hover table-sm datatable w-100">
      <thead>
        <tr>
          <th>#</th>
          <th>Nama</th>
          <th>Username</th>
          <th class="text-end text-nowrap">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $i => $u): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td>
              <?= esc($u['nama_user']) ?>
              <?php if ((int) $u['iduser'] === $iduserSaatIni): ?>
                <span class="badge bg-label-primary ms-1">Anda</span>
              <?php endif; ?>
            </td>
            <td><?= esc($u['username']) ?></td>
            <td class="text-end text-nowrap">
              <button
                type="button"
                class="btn btn-icon btn-text-secondary rounded-pill"
                data-bs-toggle="modal"
                data-bs-target="#modalEditUser<?= $u['iduser'] ?>">
                <i class="icon-base ti tabler-edit"></i>
              </button>
              <?php if ((int) $u['iduser'] !== $iduserSaatIni): ?>
                <form
                  action="<?= base_url('user/delete/' . $u['iduser']) ?>"
                  method="post"
                  class="d-inline"
                  onsubmit="return confirm('Hapus user ini?')">
                  <?= csrf_field() ?>
                  <button type="submit" class="btn btn-icon btn-text-danger rounded-pill">
                    <i class="icon-base ti tabler-trash"></i>
                  </button>
                </form>
              <?php endif; ?>
            </td>
          </tr>

          <!-- Edit modal -->
          <div class="modal fade" id="modalEditUser<?= $u['iduser'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <form action="<?= base_url('user/update/' . $u['iduser']) ?>" method="post">
                  <?= csrf_field() ?>
                  <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label class="form-label">Nama</label>
                      <input type="text" name="nama_user" class="form-control" value="<?= esc($u['nama_user']) ?>" required />
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Username</label>
                      <input type="text" name="username" class="form-control" value="<?= esc($u['username']) ?>" required />
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Password</label>
                      <input type="password" name="password" class="form-control" minlength="6" autocomplete="new-password" />
                      <small class="text-body-secondary">Kosongkan jika tidak ingin mengganti password.</small>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Tambah modal -->
<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="<?= base_url('user/store') ?>" method="post">
        <?= csrf_field() ?>
        <div class="modal-header">
          <h5 class="modal-title">Tambah User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="nama_user" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" minlength="6" autocomplete="new-password" required />
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
