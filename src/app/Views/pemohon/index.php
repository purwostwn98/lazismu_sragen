<?php
$pemohon = $pemohon ?? [];
$provinsi = $provinsi ?? [];
/**
 * Renders the 4 cascading wilayah selects (provinsi/kabupaten/kecamatan/kelurahan).
 * $selected holds current ids (empty for the "tambah" form) and $labels holds
 * the current text labels so an edit modal can show a value before its
 * dependent dropdowns have finished loading via AJAX.
 */
if (!function_exists('wilayahSelects')) {
  function wilayahSelects(array $provinsiList, array $selected = [], array $labels = []): void
  {
    $selected += ['provinsi' => '', 'kabupaten' => '', 'kecamatan' => '', 'kelurahan' => ''];
    $labels += ['kabupaten' => '', 'kecamatan' => '', 'kelurahan' => ''];
?>
    <div class="row wilayah-block" data-selected-kabupaten="<?= esc($selected['kabupaten']) ?>" data-selected-kecamatan="<?= esc($selected['kecamatan']) ?>" data-selected-kelurahan="<?= esc($selected['kelurahan']) ?>">
      <div class="col-md-6 mb-3">
        <label class="form-label">Provinsi</label>
        <select name="id_provinsi" class="form-select sel-provinsi" required>
          <option value="">-- Pilih Provinsi --</option>
          <?php foreach ($provinsiList as $p): ?>
            <option value="<?= $p['id_provinsi'] ?>" <?= (string) $selected['provinsi'] === (string) $p['id_provinsi'] ? 'selected' : '' ?>>
              <?= esc($p['nama_provinsi']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">Kabupaten/Kota</label>
        <select name="id_kabupaten" class="form-select sel-kabupaten" required>
          <?php if ($labels['kabupaten']): ?>
            <option value="<?= esc($selected['kabupaten']) ?>" selected><?= esc($labels['kabupaten']) ?></option>
          <?php else: ?>
            <option value="">-- Pilih Provinsi dahulu --</option>
          <?php endif; ?>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">Kecamatan</label>
        <select name="id_kecamatan" class="form-select sel-kecamatan" required>
          <?php if ($labels['kecamatan']): ?>
            <option value="<?= esc($selected['kecamatan']) ?>" selected><?= esc($labels['kecamatan']) ?></option>
          <?php else: ?>
            <option value="">-- Pilih Kabupaten dahulu --</option>
          <?php endif; ?>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">Kelurahan/Desa</label>
        <select name="id_kelurahan" class="form-select sel-kelurahan" required>
          <?php if ($labels['kelurahan']): ?>
            <option value="<?= esc($selected['kelurahan']) ?>" selected><?= esc($labels['kelurahan']) ?></option>
          <?php else: ?>
            <option value="">-- Pilih Kecamatan dahulu --</option>
          <?php endif; ?>
        </select>
      </div>
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
    <h5 class="mb-0">Data Pemohon</h5>
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPemohon">
      <i class="icon-base ti tabler-plus me-1"></i>Tambah Pemohon
    </button>
  </div>
  <div class="table-responsive">
    <table class="table table-hover table-sm datatable w-100">
      <thead>
        <tr>
          <th>#</th>
          <th>NIK</th>
          <th>Nama</th>
          <th>Jenis Kelamin</th>
          <th>Alamat</th>
          <th>Telepon</th>
          <th class="text-end text-nowrap">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pemohon as $i => $p): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><?= esc($p['nik']) ?></td>
            <td><?= esc($p['nama_pemohon']) ?></td>
            <td><?= esc($p['jenis_kelamin']) ?></td>
            <td>
              <?= esc($p['alamat_detail']) ?>,
              <?= esc($p['nama_kelurahan'] ?? '-') ?>, <?= esc($p['nama_kecamatan'] ?? '-') ?>,
              <?= esc($p['nama_kabupaten'] ?? '-') ?>, <?= esc($p['nama_provinsi'] ?? '-') ?>
            </td>
            <td><?= esc($p['telepon']) ?></td>
            <td class="text-end text-nowrap">
              <button
                type="button"
                class="btn btn-icon btn-text-secondary rounded-pill"
                data-bs-toggle="modal"
                data-bs-target="#modalEditPemohon<?= $i ?>">
                <i class="icon-base ti tabler-edit"></i>
              </button>
              <form
                action="<?= base_url('pemohon/delete/' . $p['nik']) ?>"
                method="post"
                class="d-inline"
                onsubmit="return confirm('Hapus data pemohon ini?')">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-icon btn-text-danger rounded-pill">
                  <i class="icon-base ti tabler-trash"></i>
                </button>
              </form>
            </td>
          </tr>

          <!-- Edit modal -->
          <div class="modal fade modal-pemohon" id="modalEditPemohon<?= $i ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
              <div class="modal-content">
                <form action="<?= base_url('pemohon/update/' . $p['nik']) ?>" method="post">
                  <?= csrf_field() ?>
                  <div class="modal-header">
                    <h5 class="modal-title">Edit Pemohon</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label class="form-label">NIK</label>
                        <input type="text" class="form-control" value="<?= esc($p['nik']) ?>" disabled />
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama_pemohon" class="form-control" value="<?= esc($p['nama_pemohon']) ?>" required />
                      </div>
                      <div class="col-md-4 mb-3">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select" required>
                          <option value="Laki-laki" <?= $p['jenis_kelamin'] === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                          <option value="Perempuan" <?= $p['jenis_kelamin'] === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                      </div>
                      <div class="col-md-4 mb-3">
                        <label class="form-label">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="form-control" value="<?= esc($p['tempat_lahir']) ?>" required />
                      </div>
                      <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control" value="<?= esc($p['tanggal_lahir']) ?>" required />
                      </div>
                    </div>

                    <?php
                    wilayahSelects($provinsi, [
                      'provinsi'  => $p['id_provinsi'],
                      'kabupaten' => $p['id_kabupaten'],
                      'kecamatan' => $p['id_kecamatan'],
                      'kelurahan' => $p['id_kelurahan'],
                    ], [
                      'kabupaten' => $p['nama_kabupaten'] ?? '',
                      'kecamatan' => $p['nama_kecamatan'] ?? '',
                      'kelurahan' => $p['nama_kelurahan'] ?? '',
                    ]);
                    ?>

                    <div class="mb-3">
                      <label class="form-label">Alamat Detail</label>
                      <textarea name="alamat_detail" class="form-control" rows="2" required><?= esc($p['alamat_detail']) ?></textarea>
                    </div>
                    <div class="row">
                      <div class="col-md-4 mb-3">
                        <label class="form-label">Agama</label>
                        <select name="agama" class="form-select" required>
                          <?php foreach (['Islam', 'Protestan', 'Katolik', 'Hindhu', 'Budha'] as $ag): ?>
                            <option value="<?= $ag ?>" <?= $p['agama'] === $ag ? 'selected' : '' ?>><?= $ag ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                      <div class="col-md-4 mb-3">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="telepon" class="form-control" value="<?= esc($p['telepon']) ?>" required />
                      </div>
                      <div class="col-md-4 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= esc($p['email']) ?>" required />
                      </div>
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
<div class="modal fade modal-pemohon" id="modalTambahPemohon" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form action="<?= base_url('pemohon/store') ?>" method="post">
        <?= csrf_field() ?>
        <div class="modal-header">
          <h5 class="modal-title">Tambah Pemohon</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">NIK</label>
              <input type="text" name="nik" class="form-control" maxlength="100" required />
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Nama</label>
              <input type="text" name="nama_pemohon" class="form-control" required />
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Jenis Kelamin</label>
              <select name="jenis_kelamin" class="form-select" required>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
              </select>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Tempat Lahir</label>
              <input type="text" name="tempat_lahir" class="form-control" required />
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Tanggal Lahir</label>
              <input type="date" name="tanggal_lahir" class="form-control" required />
            </div>
          </div>

          <?php wilayahSelects($provinsi); ?>

          <div class="mb-3">
            <label class="form-label">Alamat Detail</label>
            <textarea name="alamat_detail" class="form-control" rows="2" required></textarea>
          </div>
          <div class="row">
            <div class="col-md-4 mb-3">
              <label class="form-label">Agama</label>
              <select name="agama" class="form-select" required>
                <?php foreach (['Islam', 'Protestan', 'Katolik', 'Hindhu', 'Budha'] as $ag): ?>
                  <option value="<?= $ag ?>"><?= $ag ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Telepon</label>
              <input type="text" name="telepon" class="form-control" required />
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" required />
            </div>
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

<?= $this->section('pageScripts') ?>
<script src="<?= base_url('assets/js/wilayah-cascade.js') ?>"></script>
<?= $this->endSection() ?>