<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <h5 class="mb-0">Data Kategori Program</h5>
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
      <i class="icon-base ti tabler-plus me-1"></i>Tambah Kategori
    </button>
  </div>
  <div class="table-responsive">
    <table class="table table-hover table-sm datatable w-100">
      <thead>
        <tr>
          <th>#</th>
          <th>Nama Kategori</th>
          <th>Pilar</th>
          <th>Deskripsi</th>
          <th>Status</th>
          <th class="text-end text-nowrap">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($kategori as $i => $k): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><?= esc($k['nama_kategori']) ?></td>
            <td><?= esc($k['nama_pilar'] ?? '-') ?></td>
            <td><?= esc($k['deskripsi_kategori']) ?></td>
            <td>
              <?php if ((int) $k['status_kategori'] === 1): ?>
                <span class="badge bg-label-success">Aktif</span>
              <?php else: ?>
                <span class="badge bg-label-secondary">Nonaktif</span>
              <?php endif; ?>
            </td>
            <td class="text-end text-nowrap">
              <button
                type="button"
                class="btn btn-icon btn-text-secondary rounded-pill"
                data-bs-toggle="modal"
                data-bs-target="#modalEditKategori<?= $k['id_kategori_program'] ?>">
                <i class="icon-base ti tabler-edit"></i>
              </button>
              <form
                action="<?= base_url('kategori-program/delete/' . $k['id_kategori_program']) ?>"
                method="post"
                class="d-inline"
                onsubmit="return confirm('Hapus kategori ini?')">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-icon btn-text-danger rounded-pill">
                  <i class="icon-base ti tabler-trash"></i>
                </button>
              </form>
            </td>
          </tr>

          <!-- Edit modal -->
          <div class="modal fade" id="modalEditKategori<?= $k['id_kategori_program'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <form action="<?= base_url('kategori-program/update/' . $k['id_kategori_program']) ?>" method="post">
                  <?= csrf_field() ?>
                  <div class="modal-header">
                    <h5 class="modal-title">Edit Kategori Program</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label class="form-label">Pilar</label>
                      <select name="id_pilar" class="form-select" required>
                        <?php foreach ($pilar as $p): ?>
                          <option value="<?= $p['id_pilar'] ?>" <?= (int) $k['id_pilar'] === (int) $p['id_pilar'] ? 'selected' : '' ?>>
                            <?= esc($p['nama_pilar']) ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Nama Kategori</label>
                      <input type="text" name="nama_kategori" class="form-control" value="<?= esc($k['nama_kategori']) ?>" required />
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Deskripsi</label>
                      <textarea name="deskripsi_kategori" class="form-control" rows="2" required><?= esc($k['deskripsi_kategori']) ?></textarea>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Status</label>
                      <select name="status_kategori" class="form-select" required>
                        <option value="1" <?= (int) $k['status_kategori'] === 1 ? 'selected' : '' ?>>Aktif</option>
                        <option value="0" <?= (int) $k['status_kategori'] === 0 ? 'selected' : '' ?>>Nonaktif</option>
                      </select>
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
<div class="modal fade" id="modalTambahKategori" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="<?= base_url('kategori-program/store') ?>" method="post">
        <?= csrf_field() ?>
        <div class="modal-header">
          <h5 class="modal-title">Tambah Kategori Program</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Pilar</label>
            <select name="id_pilar" class="form-select" required>
              <option value="">-- Pilih Pilar --</option>
              <?php foreach ($pilar as $p): ?>
                <option value="<?= $p['id_pilar'] ?>"><?= esc($p['nama_pilar']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Nama Kategori</label>
            <input type="text" name="nama_kategori" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi_kategori" class="form-control" rows="2" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status_kategori" class="form-select" required>
              <option value="1">Aktif</option>
              <option value="0">Nonaktif</option>
            </select>
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
