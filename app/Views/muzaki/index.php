<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <h5 class="mb-0">Data Muzaki</h5>
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahMuzaki">
      <i class="icon-base ti tabler-plus me-1"></i>Tambah Muzaki
    </button>
  </div>
  <div class="table-responsive">
    <table class="table table-hover table-sm datatable w-100">
      <thead>
        <tr>
          <th>#</th>
          <th>ID Muzaki</th>
          <th>Nama</th>
          <th>Alamat</th>
          <th>Telepon</th>
          <th>Email</th>
          <th>Jenis</th>
          <th class="text-end text-nowrap">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($muzaki as $i => $m): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><?= esc($m['id_muzaki']) ?></td>
            <td><?= esc($m['nama_muzaki']) ?></td>
            <td><?= esc($m['alamat_muzaki']) ?></td>
            <td><?= esc($m['tlp_muzaki']) ?></td>
            <td><?= esc($m['email_muzaki']) ?></td>
            <td><?= esc($m['jenis_muzaki']) ?></td>
            <td class="text-end text-nowrap">
              <button
                type="button"
                class="btn btn-icon btn-text-secondary rounded-pill"
                data-bs-toggle="modal"
                data-bs-target="#modalEditMuzaki<?= $i ?>">
                <i class="icon-base ti tabler-edit"></i>
              </button>
              <form
                action="<?= base_url('muzaki/delete/' . $m['id_muzaki']) ?>"
                method="post"
                class="d-inline"
                onsubmit="return confirm('Hapus data muzaki ini?')">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-icon btn-text-danger rounded-pill">
                  <i class="icon-base ti tabler-trash"></i>
                </button>
              </form>
            </td>
          </tr>

          <!-- Edit modal -->
          <div class="modal fade" id="modalEditMuzaki<?= $i ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <form action="<?= base_url('muzaki/update/' . $m['id_muzaki']) ?>" method="post">
                  <?= csrf_field() ?>
                  <div class="modal-header">
                    <h5 class="modal-title">Edit Muzaki</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label class="form-label">Nama Muzaki</label>
                      <input type="text" name="nama_muzaki" class="form-control" value="<?= esc($m['nama_muzaki']) ?>" required />
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Alamat</label>
                      <textarea name="alamat_muzaki" class="form-control" rows="2"><?= esc($m['alamat_muzaki']) ?></textarea>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Telepon</label>
                      <input type="text" name="tlp_muzaki" class="form-control" value="<?= esc($m['tlp_muzaki']) ?>" required />
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Email</label>
                      <input type="email" name="email_muzaki" class="form-control" value="<?= esc($m['email_muzaki']) ?>" required />
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Jenis Muzaki</label>
                      <select name="jenis_muzaki" class="form-select">
                        <?php foreach (['Laki-laki', 'Perempuan', 'Lembaga'] as $jenis): ?>
                          <option value="<?= $jenis ?>" <?= $m['jenis_muzaki'] === $jenis ? 'selected' : '' ?>><?= $jenis ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="form-check">
                      <input
                        type="checkbox"
                        name="is_dosen"
                        class="form-check-input"
                        value="1"
                        id="isDosenEdit<?= $i ?>"
                        <?= $m['is_dosen'] ? 'checked' : '' ?> />
                      <label class="form-check-label" for="isDosenEdit<?= $i ?>">Dosen</label>
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
<div class="modal fade" id="modalTambahMuzaki" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="<?= base_url('muzaki/store') ?>" method="post">
        <?= csrf_field() ?>
        <div class="modal-header">
          <h5 class="modal-title">Tambah Muzaki</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Muzaki</label>
            <input type="text" name="nama_muzaki" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Alamat</label>
            <textarea name="alamat_muzaki" class="form-control" rows="2"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Telepon</label>
            <input type="text" name="tlp_muzaki" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email_muzaki" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Jenis Muzaki</label>
            <select name="jenis_muzaki" class="form-select">
              <option value="Laki-laki">Laki-laki</option>
              <option value="Perempuan">Perempuan</option>
              <option value="Lembaga">Lembaga</option>
            </select>
          </div>
          <div class="form-check">
            <input type="checkbox" name="is_dosen" class="form-check-input" value="1" id="isDosenTambah" />
            <label class="form-check-label" for="isDosenTambah">Dosen</label>
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
