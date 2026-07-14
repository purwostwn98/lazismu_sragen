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
              <i class="icon-base ti tabler-file-description icon-lg"></i>
            </span>
          </div>
          <h4 class="mb-1">Ajukan Bantuan</h4>
          <p class="text-body-secondary mb-0">
            Masukkan NIK Anda untuk memulai. Jika sudah pernah mendaftar, Anda akan langsung diarahkan ke formulir
            ajuan.
          </p>
        </div>

        <form action="<?= base_url('pengajuan/cek') ?>" method="post">
          <?= csrf_field() ?>
          <div class="mb-4">
            <label for="nik" class="form-label">NIK (16 digit)</label>
            <input
              type="text"
              class="form-control"
              id="nik"
              name="nik"
              inputmode="numeric"
              maxlength="16"
              pattern="\d{16}"
              placeholder="Masukkan NIK Anda"
              autofocus
              required />
          </div>
          <button type="submit" class="btn btn-primary d-grid w-100">Lanjutkan</button>
        </form>

        <p class="text-center mt-4 mb-0">
          Sudah pernah mengajukan? <a href="<?= base_url('pengajuan/status') ?>">Cek status ajuan</a>
        </p>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
