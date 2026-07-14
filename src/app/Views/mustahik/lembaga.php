<?php
$lembaga = $lembaga ?? [];

if (!function_exists('lembagaFields')) {
  function lembagaFields(array $l = []): void
  {
    $l += [
      'nomor_legalitas' => '', 'nama_lembaga' => '', 'bidang' => '', 'tahun_berdiri' => '',
      'npwp' => '', 'alamat' => '', 'nomor_telepon' => '', 'email' => '', 'website' => '',
      'nama_pj' => '', 'jabatan_pj' => '', 'sumber_pendanaan' => '', 'nomor_rekening' => '',
    ];
?>
    <div class="col-md-8 mb-3">
      <label class="form-label">Nomor Legalitas Lembaga (Akta/Izin Operasional/NIB)</label>
      <input type="text" name="nomor_legalitas" class="form-control" value="<?= esc($l['nomor_legalitas']) ?>" required />
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">Tahun Berdiri</label>
      <input type="number" name="tahun_berdiri" class="form-control" min="1900" max="<?= date('Y') ?>" value="<?= esc($l['tahun_berdiri']) ?>" />
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">Nama Lembaga</label>
      <input type="text" name="nama_lembaga" class="form-control" value="<?= esc($l['nama_lembaga']) ?>" required />
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">Bidang</label>
      <input type="text" name="bidang" class="form-control" placeholder="mis. Pendidikan, Sosial, Dakwah" value="<?= esc($l['bidang']) ?>" required />
    </div>
    <div class="col-12 mb-3">
      <label class="form-label">Alamat Lembaga</label>
      <input type="text" name="alamat" class="form-control" value="<?= esc($l['alamat']) ?>" required />
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">NPWP</label>
      <input type="text" name="npwp" class="form-control" value="<?= esc($l['npwp']) ?>" />
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">Telepon</label>
      <input type="text" name="nomor_telepon" class="form-control" value="<?= esc($l['nomor_telepon']) ?>" required />
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" value="<?= esc($l['email']) ?>" required />
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">Website</label>
      <input type="text" name="website" class="form-control" value="<?= esc($l['website']) ?>" />
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">Nomor Rekening</label>
      <input type="text" name="nomor_rekening" class="form-control" value="<?= esc($l['nomor_rekening']) ?>" />
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">Nama Penanggung Jawab</label>
      <input type="text" name="nama_pj" class="form-control" value="<?= esc($l['nama_pj']) ?>" required />
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">Jabatan Penanggung Jawab</label>
      <input type="text" name="jabatan_pj" class="form-control" value="<?= esc($l['jabatan_pj']) ?>" required />
    </div>
    <div class="col-12 mb-3">
      <label class="form-label">Sumber Pendanaan</label>
      <input type="text" name="sumber_pendanaan" class="form-control" value="<?= esc($l['sumber_pendanaan']) ?>" />
    </div>
<?php
  }
}
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <h5 class="mb-0">Data Mustahik Lembaga</h5>
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahLembaga">
      <i class="icon-base ti tabler-plus me-1"></i>Tambah Lembaga
    </button>
  </div>
  <div class="table-responsive">
    <table class="table table-hover table-sm datatable w-100">
      <thead>
        <tr>
          <th>#</th>
          <th>Nomor Legalitas</th>
          <th>Nama Lembaga</th>
          <th>Bidang</th>
          <th>Penanggung Jawab</th>
          <th>Telepon</th>
          <th class="text-end text-nowrap">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($lembaga as $i => $l): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><?= esc($l['nomor_legalitas']) ?></td>
            <td><?= esc($l['nama_lembaga']) ?></td>
            <td><?= esc($l['bidang']) ?></td>
            <td><?= esc($l['nama_pj']) ?> <span class="text-body-secondary">(<?= esc($l['jabatan_pj']) ?>)</span></td>
            <td><?= esc($l['nomor_telepon']) ?></td>
            <td class="text-end text-nowrap">
              <button
                type="button"
                class="btn btn-icon btn-text-secondary rounded-pill"
                data-bs-toggle="modal"
                data-bs-target="#modalEditLembaga<?= $l['id_ms_lembaga'] ?>">
                <i class="icon-base ti tabler-edit"></i>
              </button>
              <form
                action="<?= base_url('mustahik/lembaga/delete/' . $l['id_ms_lembaga']) ?>"
                method="post"
                class="d-inline"
                onsubmit="return confirm('Hapus data lembaga ini?')">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-icon btn-text-danger rounded-pill">
                  <i class="icon-base ti tabler-trash"></i>
                </button>
              </form>
            </td>
          </tr>

          <!-- Edit modal -->
          <div class="modal fade" id="modalEditLembaga<?= $l['id_ms_lembaga'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
              <div class="modal-content">
                <form action="<?= base_url('mustahik/lembaga/update/' . $l['id_ms_lembaga']) ?>" method="post">
                  <?= csrf_field() ?>
                  <div class="modal-header">
                    <h5 class="modal-title">Edit Lembaga</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="row">
                      <?php lembagaFields($l); ?>
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
<div class="modal fade" id="modalTambahLembaga" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form action="<?= base_url('mustahik/lembaga/store') ?>" method="post">
        <?= csrf_field() ?>
        <div class="modal-header">
          <h5 class="modal-title">Tambah Lembaga</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <?php lembagaFields(); ?>
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
