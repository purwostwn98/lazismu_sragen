<?php
$lembaga = $lembaga ?? [];
$provinsi = $provinsi ?? [];

/** Same 4 cascading wilayah selects as mustahik/individu.php's wilayahSelectsMustahik(), scoped to this file. */
if (!function_exists('wilayahSelectsLembaga')) {
  function wilayahSelectsLembaga(array $provinsiList, array $selected = [], array $labels = []): void
  {
    $selected += ['provinsi' => '', 'kabupaten' => '', 'kecamatan' => '', 'kelurahan' => ''];
    $labels += ['kabupaten' => '', 'kecamatan' => '', 'kelurahan' => ''];
?>
    <div class="row wilayah-block" data-selected-kabupaten="<?= esc($selected['kabupaten']) ?>" data-selected-kecamatan="<?= esc($selected['kecamatan']) ?>" data-selected-kelurahan="<?= esc($selected['kelurahan']) ?>">
      <div class="col-md-6 mb-3">
        <label class="form-label">Provinsi</label>
        <select name="provinsi" class="form-select sel-provinsi">
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
        <select name="kabupaten" class="form-select sel-kabupaten">
          <?php if ($labels['kabupaten']): ?>
            <option value="<?= esc($selected['kabupaten']) ?>" selected><?= esc($labels['kabupaten']) ?></option>
          <?php else: ?>
            <option value="">-- Pilih Provinsi dahulu --</option>
          <?php endif; ?>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">Kecamatan</label>
        <select name="kecamatan" class="form-select sel-kecamatan">
          <?php if ($labels['kecamatan']): ?>
            <option value="<?= esc($selected['kecamatan']) ?>" selected><?= esc($labels['kecamatan']) ?></option>
          <?php else: ?>
            <option value="">-- Pilih Kabupaten dahulu --</option>
          <?php endif; ?>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">Kelurahan/Desa</label>
        <select name="desa" class="form-select sel-kelurahan">
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

if (!function_exists('lembagaFields')) {
  function lembagaFields(array $provinsiList, array $l = []): void
  {
    $l += [
      'nomor_legalitas' => '', 'nama_lembaga' => '', 'bidang' => '', 'tahun_berdiri' => '',
      'npwp' => '', 'dusun' => '', 'rt' => '', 'rw' => '', 'nomor_telepon' => '', 'email' => '', 'website' => '',
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

    <?php
    wilayahSelectsLembaga($provinsiList, [
      'provinsi'  => $l['provinsi'] ?? '',
      'kabupaten' => $l['kabupaten'] ?? '',
      'kecamatan' => $l['kecamatan'] ?? '',
      'kelurahan' => $l['desa'] ?? '',
    ], [
      'kabupaten' => $l['nama_kabupaten'] ?? '',
      'kecamatan' => $l['nama_kecamatan'] ?? '',
      'kelurahan' => $l['nama_kelurahan'] ?? '',
    ]);
    ?>

    <div class="col-md-6 mb-3">
      <label class="form-label">Dusun / Nama Jalan</label>
      <input type="text" name="dusun" class="form-control" value="<?= esc($l['dusun']) ?>" required />
    </div>
    <div class="col-md-3 mb-3">
      <label class="form-label">RT</label>
      <input type="number" name="rt" class="form-control" min="0" value="<?= esc($l['rt']) ?>" />
    </div>
    <div class="col-md-3 mb-3">
      <label class="form-label">RW</label>
      <input type="number" name="rw" class="form-control" min="0" value="<?= esc($l['rw']) ?>" />
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
          <div class="modal fade modal-mustahik" id="modalEditLembaga<?= $l['id_ms_lembaga'] ?>" tabindex="-1" aria-hidden="true">
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
                      <?php lembagaFields($provinsi, $l); ?>
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
<div class="modal fade modal-mustahik" id="modalTambahLembaga" tabindex="-1" aria-hidden="true">
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
            <?php lembagaFields($provinsi); ?>
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
