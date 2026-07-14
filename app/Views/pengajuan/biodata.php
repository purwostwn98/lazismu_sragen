<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
  <div class="col-md-9 col-lg-8">
    <?= $this->include('partials/alerts') ?>

    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Biodata Pemohon</h5>
        <small class="text-body-secondary">NIK <?= esc($nik) ?> belum terdaftar. Lengkapi biodata Anda terlebih dahulu.</small>
      </div>
      <div class="card-body">
        <form action="<?= base_url('pengajuan/biodata/store') ?>" method="post">
          <?= csrf_field() ?>
          <input type="hidden" name="nik" value="<?= esc($nik) ?>" />

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">NIK</label>
              <input type="text" class="form-control" value="<?= esc($nik) ?>" disabled />
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Nama Lengkap</label>
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

          <div class="row wilayah-block">
            <div class="col-md-6 mb-3">
              <label class="form-label">Provinsi</label>
              <select name="id_provinsi" class="form-select sel-provinsi" required>
                <option value="">-- Pilih Provinsi --</option>
                <?php foreach ($provinsi as $p): ?>
                  <option value="<?= $p['id_provinsi'] ?>"><?= esc($p['nama_provinsi']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Kabupaten/Kota</label>
              <select name="id_kabupaten" class="form-select sel-kabupaten" required>
                <option value="">-- Pilih Provinsi dahulu --</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Kecamatan</label>
              <select name="id_kecamatan" class="form-select sel-kecamatan" required>
                <option value="">-- Pilih Kabupaten dahulu --</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Kelurahan/Desa</label>
              <select name="id_kelurahan" class="form-select sel-kelurahan" required>
                <option value="">-- Pilih Kecamatan dahulu --</option>
              </select>
            </div>
          </div>

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

          <button type="submit" class="btn btn-primary">Simpan &amp; Lanjutkan</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script src="<?= base_url('assets/js/wilayah-cascade.js') ?>"></script>
<?= $this->endSection() ?>
