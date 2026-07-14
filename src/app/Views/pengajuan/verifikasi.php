<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
  <div class="col-md-7">
    <div class="card">
      <div class="card-body p-6 text-center">
        <?php if ($ajuan): ?>
          <div class="avatar avatar-lg mx-auto mb-3">
            <span class="avatar-initial rounded-circle bg-label-success">
              <i class="icon-base ti tabler-shield-check icon-lg"></i>
            </span>
          </div>
          <h4 class="mb-1">Dokumen Terverifikasi Sah</h4>
          <p class="text-body-secondary">
            Dokumen dengan nomor ajuan di bawah ini benar tercatat dalam sistem resmi Lazismu Sragen.
          </p>

          <div class="border rounded-2 p-4 my-4 bg-label-success text-start">
            <div class="row g-2">
              <div class="col-5 text-body-secondary">Nomor Ajuan</div>
              <div class="col-7"><strong><?= esc($ajuan['nomor_ajuan']) ?></strong></div>

              <div class="col-5 text-body-secondary">Nama Pengaju</div>
              <div class="col-7"><?= esc($ajuan['nama_pemohon'] ?? '-') ?></div>

              <div class="col-5 text-body-secondary">Kegiatan Bantuan</div>
              <div class="col-7"><?= esc($ajuan['nama_program'] ?? '-') ?></div>

              <div class="col-5 text-body-secondary">Tanggal Diajukan</div>
              <div class="col-7"><?= esc($ajuan['tgl_diajukan']) ?></div>

              <div class="col-5 text-body-secondary">Status Ajuan</div>
              <div class="col-7"><?= esc($ajuan['keterangan_status'] ?? '-') ?></div>
            </div>
          </div>
        <?php else: ?>
          <div class="avatar avatar-lg mx-auto mb-3">
            <span class="avatar-initial rounded-circle bg-label-danger">
              <i class="icon-base ti tabler-shield-x icon-lg"></i>
            </span>
          </div>
          <h4 class="mb-1">Dokumen Tidak Ditemukan</h4>
          <p class="text-body-secondary">
            Nomor ajuan pada kode QR ini tidak ditemukan dalam sistem. Dokumen mungkin tidak sah atau telah diubah.
          </p>
        <?php endif; ?>

        <div class="d-flex justify-content-center gap-2 mt-4">
          <a href="<?= base_url('/') ?>" class="btn btn-label-secondary">Kembali ke Beranda</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
