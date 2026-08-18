<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <h5 class="mb-0">Data Penjabat</h5>
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPenjabat">
      <i class="icon-base ti tabler-plus me-1"></i>Tambah Penjabat
    </button>
  </div>
  <div class="table-responsive">
    <table class="table table-hover table-sm datatable w-100">
      <thead>
        <tr>
          <th>#</th>
          <th>Jabatan</th>
          <th>Nama Penjabat</th>
          <th>Email</th>
          <th>Mulai Tahun</th>
          <th class="text-end text-nowrap">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($penjabat as $i => $p): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><?= esc($p['nama_jabatan']) ?></td>
            <td><?= esc($p['nama_penjabat']) ?></td>
            <td><?= esc($p['email'] ?? '-') ?></td>
            <td><?= esc($p['mulai_tahun'] ?? '-') ?></td>
            <td class="text-end text-nowrap">
              <button
                type="button"
                class="btn btn-icon btn-text-secondary rounded-pill"
                data-bs-toggle="modal"
                data-bs-target="#modalEditPenjabat<?= $p['id'] ?>">
                <i class="icon-base ti tabler-edit"></i>
              </button>
              <form
                action="<?= base_url('penjabat/delete/' . $p['id']) ?>"
                method="post"
                class="d-inline"
                onsubmit="return confirm('Hapus data penjabat ini?')">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-icon btn-text-danger rounded-pill">
                  <i class="icon-base ti tabler-trash"></i>
                </button>
              </form>
            </td>
          </tr>

          <!-- Edit modal -->
          <div class="modal fade" id="modalEditPenjabat<?= $p['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <form action="<?= base_url('penjabat/update/' . $p['id']) ?>" method="post">
                  <?= csrf_field() ?>
                  <div class="modal-header">
                    <h5 class="modal-title">Edit Penjabat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label class="form-label">Jabatan</label>
                      <select name="id_jabatan" class="form-select" required>
                        <?php foreach ($jabatan as $j): ?>
                          <option value="<?= $j['id'] ?>" <?= (int) $p['id_jabatan'] === (int) $j['id'] ? 'selected' : '' ?>>
                            <?= esc($j['nama_jabatan']) ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Nama Penjabat</label>
                      <input type="text" name="nama_penjabat" class="form-control" value="<?= esc($p['nama_penjabat']) ?>" required />
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Email</label>
                      <input type="email" name="email" class="form-control" value="<?= esc($p['email'] ?? '') ?>" />
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Mulai Tahun</label>
                      <input type="number" name="mulai_tahun" class="form-control" min="1900" max="2100" value="<?= esc($p['mulai_tahun'] ?? '') ?>" />
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
<div class="modal fade" id="modalTambahPenjabat" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="<?= base_url('penjabat/store') ?>" method="post">
        <?= csrf_field() ?>
        <div class="modal-header">
          <h5 class="modal-title">Tambah Penjabat</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Jabatan</label>
            <select name="id_jabatan" class="form-select" required>
              <option value="">-- Pilih Jabatan --</option>
              <?php foreach ($jabatan as $j): ?>
                <option value="<?= $j['id'] ?>"><?= esc($j['nama_jabatan']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Nama Penjabat</label>
            <input type="text" name="nama_penjabat" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" />
          </div>
          <div class="mb-3">
            <label class="form-label">Mulai Tahun</label>
            <input type="number" name="mulai_tahun" class="form-control" min="1900" max="2100" />
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
