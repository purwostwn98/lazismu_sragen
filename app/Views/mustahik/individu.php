<?php
$mustahik = $mustahik ?? [];
$provinsi = $provinsi ?? [];
$pekerjaan = $pekerjaan ?? [];
$penghasilan = $penghasilan ?? [];

/**
 * Renders the 4 cascading wilayah selects (provinsi/kabupaten/kecamatan/kelurahan).
 * $selected holds current ids (empty for the "tambah" form) and $labels holds
 * the current text labels so an edit modal can show a value before its
 * dependent dropdowns have finished loading via AJAX.
 */
if (!function_exists('wilayahSelectsMustahik')) {
  function wilayahSelectsMustahik(array $provinsiList, array $selected = [], array $labels = []): void
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

if (!function_exists('mustahikFields')) {
  function mustahikFields(array $pekerjaanList, array $penghasilanList, array $provinsiList, array $m = []): void
  {
    $m += [
      'nama_mustahik' => '', 'kelamin_mustahik' => '', 'tempat_lahir' => '', 'tgl_lahir' => '',
      'agama_mustahik' => '', 'jml_keluarga' => '', 'alamat' => '', 'status_pendidikan' => '',
      'status_marital' => '', 'pekerjaan' => '', 'penghasilan' => '', 'no_handphone' => '', 'email' => '', 'kk' => '',
    ];
?>
    <div class="col-md-6 mb-3">
      <label class="form-label">Nama Mustahik</label>
      <input type="text" name="nama_mustahik" class="form-control" value="<?= esc($m['nama_mustahik']) ?>" required />
    </div>
    <div class="col-md-3 mb-3">
      <label class="form-label">Tempat Lahir</label>
      <input type="text" name="tempat_lahir" class="form-control" value="<?= esc($m['tempat_lahir']) ?>" />
    </div>
    <div class="col-md-3 mb-3">
      <label class="form-label">Tanggal Lahir</label>
      <input type="date" name="tgl_lahir" class="form-control" value="<?= esc($m['tgl_lahir']) ?>" />
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">Jenis Kelamin</label>
      <select name="kelamin_mustahik" class="form-select" required>
        <?php foreach (['Laki-laki', 'Perempuan'] as $jk): ?>
          <option value="<?= $jk ?>" <?= $m['kelamin_mustahik'] === $jk ? 'selected' : '' ?>><?= $jk ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">Agama</label>
      <select name="agama_mustahik" class="form-select">
        <?php foreach (['Islam', 'Protestan', 'Katolik', 'Hindhu', 'Budha'] as $ag): ?>
          <option value="<?= $ag ?>" <?= $m['agama_mustahik'] === $ag ? 'selected' : '' ?>><?= $ag ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">Jumlah Keluarga</label>
      <input type="number" name="jml_keluarga" class="form-control" min="0" value="<?= esc($m['jml_keluarga']) ?>" />
    </div>

    <?php
    wilayahSelectsMustahik($provinsiList, [
      'provinsi'  => $m['provinsi'] ?? '',
      'kabupaten' => $m['kabupaten'] ?? '',
      'kecamatan' => $m['kecamatan'] ?? '',
      'kelurahan' => $m['desa'] ?? '',
    ], [
      'kabupaten' => $m['nama_kabupaten'] ?? '',
      'kecamatan' => $m['nama_kecamatan'] ?? '',
      'kelurahan' => $m['nama_kelurahan'] ?? '',
    ]);
    ?>

    <div class="col-12 mb-3">
      <label class="form-label">Alamat Detail</label>
      <textarea name="alamat" class="form-control" rows="2" required><?= esc($m['alamat']) ?></textarea>
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">Status Pendidikan</label>
      <select name="status_pendidikan" class="form-select">
        <option value="">-- Pilih --</option>
        <?php foreach (['SD', 'SMP', 'SMA/SMK', 'Diploma', 'Sarjana', 'Pascasarjana', 'tidak tamat SD', 'lainnya'] as $sp): ?>
          <option value="<?= $sp ?>" <?= $m['status_pendidikan'] === $sp ? 'selected' : '' ?>><?= $sp ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">Status Marital</label>
      <select name="status_marital" class="form-select">
        <option value="">-- Pilih --</option>
        <?php foreach (['Lajang', 'Menikah', 'Cerai', 'Lainnya'] as $sm): ?>
          <option value="<?= $sm ?>" <?= $m['status_marital'] === $sm ? 'selected' : '' ?>><?= $sm ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">Pekerjaan</label>
      <select name="pekerjaan" class="form-select">
        <option value="">-- Pilih --</option>
        <?php foreach ($pekerjaanList as $pk): ?>
          <option value="<?= $pk['id_pekerjaan'] ?>" <?= (string) $m['pekerjaan'] === (string) $pk['id_pekerjaan'] ? 'selected' : '' ?>><?= esc($pk['nama_pekerjaan']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">Penghasilan</label>
      <select name="penghasilan" class="form-select">
        <option value="">-- Pilih --</option>
        <?php foreach ($penghasilanList as $pg): ?>
          <option value="<?= $pg['id_penghasilan'] ?>" <?= (string) $m['penghasilan'] === (string) $pg['id_penghasilan'] ? 'selected' : '' ?>><?= esc($pg['label_penghasilan']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">No. Handphone</label>
      <input type="text" name="no_handphone" class="form-control" value="<?= esc($m['no_handphone']) ?>" />
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" value="<?= esc($m['email']) ?>" />
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">Nomor KK</label>
      <input type="text" name="kk" class="form-control" value="<?= esc($m['kk']) ?>" />
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">Foto KTP</label>
      <input type="file" name="foto_ktp" class="form-control" />
      <?php if (!empty($m['foto_ktp'])): ?>
        <small class="text-body-secondary">
          Kosongkan jika tidak ingin mengganti. <a href="<?= base_url('mustahik/individu/' . $m['nik'] . '/dokumen/ktp') ?>" target="_blank">Lihat KTP saat ini</a>.
        </small>
      <?php endif; ?>
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">Foto KK</label>
      <input type="file" name="foto_kk" class="form-control" />
      <?php if (!empty($m['foto_kk'])): ?>
        <small class="text-body-secondary">
          Kosongkan jika tidak ingin mengganti. <a href="<?= base_url('mustahik/individu/' . $m['nik'] . '/dokumen/kk') ?>" target="_blank">Lihat KK saat ini</a>.
        </small>
      <?php endif; ?>
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
    <h5 class="mb-0">Data Mustahik Individu</h5>
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahMustahik">
      <i class="icon-base ti tabler-plus me-1"></i>Tambah Mustahik
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
          <th>No. HP</th>
          <th>Pekerjaan</th>
          <th class="text-end text-nowrap">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($mustahik as $i => $m): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><?= esc($m['nik']) ?></td>
            <td><?= esc($m['nama_mustahik']) ?></td>
            <td><?= esc($m['kelamin_mustahik']) ?></td>
            <td>
              <?= esc($m['alamat']) ?>,
              <?= esc($m['nama_kelurahan'] ?? '-') ?>, <?= esc($m['nama_kecamatan'] ?? '-') ?>,
              <?= esc($m['nama_kabupaten'] ?? '-') ?>, <?= esc($m['nama_provinsi'] ?? '-') ?>
            </td>
            <td><?= esc($m['no_handphone'] ?? '-') ?></td>
            <td><?= esc($m['nama_pekerjaan'] ?? '-') ?></td>
            <td class="text-end text-nowrap">
              <button
                type="button"
                class="btn btn-icon btn-text-secondary rounded-pill"
                data-bs-toggle="modal"
                data-bs-target="#modalEditMustahik<?= $i ?>">
                <i class="icon-base ti tabler-edit"></i>
              </button>
              <form
                action="<?= base_url('mustahik/individu/delete/' . $m['nik']) ?>"
                method="post"
                class="d-inline"
                onsubmit="return confirm('Hapus data mustahik ini?')">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-icon btn-text-danger rounded-pill">
                  <i class="icon-base ti tabler-trash"></i>
                </button>
              </form>
            </td>
          </tr>

          <!-- Edit modal -->
          <div class="modal fade modal-mustahik" id="modalEditMustahik<?= $i ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
              <div class="modal-content">
                <form action="<?= base_url('mustahik/individu/update/' . $m['nik']) ?>" method="post" enctype="multipart/form-data">
                  <?= csrf_field() ?>
                  <div class="modal-header">
                    <h5 class="modal-title">Edit Mustahik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label class="form-label">NIK</label>
                        <input type="text" class="form-control" value="<?= esc($m['nik']) ?>" disabled />
                      </div>
                      <?php mustahikFields($pekerjaan, $penghasilan, $provinsi, $m); ?>
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
<div class="modal fade modal-mustahik" id="modalTambahMustahik" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form action="<?= base_url('mustahik/individu/store') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="modal-header">
          <h5 class="modal-title">Tambah Mustahik</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">NIK</label>
              <input type="text" name="nik" class="form-control" maxlength="100" required />
            </div>
            <?php mustahikFields($pekerjaan, $penghasilan, $provinsi); ?>
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
