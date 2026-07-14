<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
  <div class="col-md-7">
    <div class="card">
      <div class="card-body p-6 text-center">
        <div class="avatar avatar-lg mx-auto mb-3">
          <span class="avatar-initial rounded-circle bg-label-success">
            <i class="icon-base ti tabler-circle-check icon-lg"></i>
          </span>
        </div>
        <h4 class="mb-1">Ajuan Berhasil Dikirim!</h4>
        <p class="text-body-secondary">
          Terima kasih, <?= esc($ajuan['nama_pemohon'] ?? '') ?>. Ajuan Anda untuk kegiatan
          <strong><?= esc($ajuan['nama_program'] ?? '-') ?></strong> telah kami terima dan akan segera diproses.
        </p>

        <div class="border rounded-2 p-4 my-4 bg-label-primary">
          <small class="d-block text-body-secondary mb-1">Nomor Ajuan Anda</small>
          <h3 class="mb-0"><?= esc($ajuan['nomor_ajuan']) ?></h3>
        </div>

        <p class="text-body-secondary small">
          Simpan nomor ajuan ini untuk mengecek status pengajuan Anda kapan saja melalui halaman
          <a href="<?= base_url('pengajuan/status') ?>">Cek Status Ajuan</a>.
        </p>

        <div class="d-flex justify-content-center gap-2 mt-4 flex-wrap">
          <a href="<?= base_url('pengajuan/sukses/' . $ajuan['nomor_ajuan'] . '/cetak') ?>" target="_blank" class="btn btn-label-primary">
            <i class="icon-base ti tabler-printer me-1"></i> Cetak Bukti PDF
          </a>
          <a href="<?= base_url('pengajuan/status') ?>" class="btn btn-primary">Cek Status Ajuan</a>
          <a href="<?= base_url('/') ?>" class="btn btn-label-secondary">Kembali ke Beranda</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
