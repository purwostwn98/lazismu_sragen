<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
  <div class="col-md-6 col-lg-5">
    <?= $this->include('partials/alerts') ?>

    <div class="card">
      <div class="card-body p-6">
        <div class="text-center mb-6">
          <div class="avatar avatar-lg mx-auto mb-3">
            <span class="avatar-initial rounded-circle bg-label-primary">
              <i class="icon-base ti tabler-search icon-lg"></i>
            </span>
          </div>
          <h4 class="mb-1">Cek Status Ajuan</h4>
          <p class="text-body-secondary mb-0">Masukkan NIK dan nomor ajuan Anda untuk melihat status terbaru.</p>
        </div>

        <form action="<?= base_url('pengajuan/status/cek') ?>" method="post">
          <?= csrf_field() ?>
          <div class="mb-4">
            <label for="nik" class="form-label">NIK</label>
            <input type="text" class="form-control" id="nik" name="nik" maxlength="16" required autofocus />
          </div>
          <div class="mb-4">
            <label for="nomor_ajuan" class="form-label">Nomor Ajuan</label>
            <input type="text" class="form-control" id="nomor_ajuan" name="nomor_ajuan" maxlength="8" required />
          </div>
          <button type="submit" class="btn btn-primary d-grid w-100">Cek Status</button>
        </form>

        <p class="text-center mt-4 mb-0">
          Belum pernah mengajukan? <a href="<?= base_url('pengajuan') ?>">Ajukan bantuan sekarang</a>
        </p>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
