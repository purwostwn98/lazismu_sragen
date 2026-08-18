<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <h5 class="mb-0">Data Jabatan</h5>
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahJabatan">
      <i class="icon-base ti tabler-plus me-1"></i>Tambah Jabatan
    </button>
  </div>
  <div class="table-responsive">
    <table class="table table-hover table-sm datatable w-100">
      <thead>
        <tr>
          <th>#</th>
          <th>Kode Jabatan</th>
          <th>Nama Jabatan</th>
          <th class="text-end text-nowrap">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($jabatan as $i => $j): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><?= esc($j['kode_jabatan']) ?></td>
            <td><?= esc($j['nama_jabatan']) ?></td>
            <td class="text-end text-nowrap">
              <button
                type="button"
                class="btn btn-icon btn-text-secondary rounded-pill"
                data-bs-toggle="modal"
                data-bs-target="#modalEditJabatan<?= $j['id'] ?>">
                <i class="icon-base ti tabler-edit"></i>
              </button>
              <form
                action="<?= base_url('jabatan/delete/' . $j['id']) ?>"
                method="post"
                class="d-inline"
                onsubmit="return confirm('Hapus jabatan ini?')">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-icon btn-text-danger rounded-pill">
                  <i class="icon-base ti tabler-trash"></i>
                </button>
              </form>
            </td>
          </tr>

          <!-- Edit modal -->
          <div class="modal fade" id="modalEditJabatan<?= $j['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <form action="<?= base_url('jabatan/update/' . $j['id']) ?>" method="post">
                  <?= csrf_field() ?>
                  <div class="modal-header">
                    <h5 class="modal-title">Edit Jabatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label class="form-label">Kode Jabatan</label>
                      <input type="text" name="kode_jabatan" class="form-control" value="<?= esc($j['kode_jabatan']) ?>" required />
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Nama Jabatan</label>
                      <input type="text" name="nama_jabatan" class="form-control" value="<?= esc($j['nama_jabatan']) ?>" required />
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
<div class="modal fade" id="modalTambahJabatan" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="<?= base_url('jabatan/store') ?>" method="post">
        <?= csrf_field() ?>
        <div class="modal-header">
          <h5 class="modal-title">Tambah Jabatan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Kode Jabatan</label>
            <input type="text" name="kode_jabatan" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Nama Jabatan</label>
            <input type="text" name="nama_jabatan" class="form-control" required />
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
