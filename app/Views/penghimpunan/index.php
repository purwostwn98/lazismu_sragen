<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <h5 class="mb-0">Data Penghimpunan</h5>
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahHimpun">
      <i class="icon-base ti tabler-plus me-1"></i>Tambah Penghimpunan
    </button>
  </div>
  <div class="table-responsive">
    <table class="table table-hover table-sm datatable w-100">
      <thead>
        <tr>
          <th>#</th>
          <th>ID Himpun</th>
          <th>Muzaki</th>
          <th>Tanggal</th>
          <th>Jumlah</th>
          <th>Via</th>
          <th class="text-end text-nowrap">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($penghimpunan as $i => $h): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><?= esc($h['id_himpun']) ?></td>
            <td><?= esc($h['nama_muzaki'] ?? $h['email_muzaki']) ?></td>
            <td><?= esc($h['tanggal_himpun']) ?></td>
            <td>Rp <?= number_format((float) $h['jumlah_himpun'], 0, ',', '.') ?></td>
            <td><span class="badge bg-label-info text-capitalize"><?= esc($h['via_himpun']) ?></span></td>
            <td class="text-end text-nowrap">
              <form
                action="<?= base_url('penghimpunan/delete/' . $h['id_himpun']) ?>"
                method="post"
                class="d-inline"
                onsubmit="return confirm('Hapus data penghimpunan ini?')">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-icon btn-text-danger rounded-pill">
                  <i class="icon-base ti tabler-trash"></i>
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Tambah modal -->
<div class="modal fade" id="modalTambahHimpun" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="<?= base_url('penghimpunan/store') ?>" method="post">
        <?= csrf_field() ?>
        <div class="modal-header">
          <h5 class="modal-title">Tambah Penghimpunan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Muzaki</label>
            <select name="email_muzaki" class="form-select" required>
              <option value="">-- Pilih Muzaki --</option>
              <?php foreach ($muzaki as $m): ?>
                <option value="<?= esc($m['email_muzaki']) ?>"><?= esc($m['nama_muzaki']) ?> (<?= esc($m['email_muzaki']) ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Tanggal Himpun</label>
            <input type="date" name="tanggal_himpun" class="form-control" required />
          </div>
          <div class="row">
            <div class="col-6 mb-3">
              <label class="form-label">Kategori</label>
              <select name="ktg_himpun" id="ktgHimpun" class="form-select" required>
                <option value="">-- Pilih --</option>
                <?php foreach ($ktg as $k): ?>
                  <option value="<?= $k['id_ktg'] ?>"><?= esc($k['keterangan_ktg']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-6 mb-3">
              <label class="form-label">Sub Kategori</label>
              <select name="sub_ktg_himpun" id="subKtgHimpun" class="form-select">
                <option value="">-- Pilih Kategori dahulu --</option>
                <?php foreach ($subktg as $s): ?>
                  <option value="<?= $s['id_sub_ktg'] ?>" data-ktg="<?= $s['id_ktg_himpun'] ?>" hidden>
                    <?= esc($s['keterangan_sub']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Jumlah (Rp)</label>
            <input type="number" step="0.01" name="jumlah_himpun" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Via</label>
            <select name="via_himpun" class="form-select" required>
              <option value="tunai">Tunai</option>
              <option value="transfer">Transfer</option>
              <option value="barang">Barang</option>
            </select>
          </div>
          <div class="row">
            <div class="col-6 mb-3">
              <label class="form-label">Tgl Setor Bank</label>
              <input type="date" name="tgl_setor_bank" class="form-control" />
            </div>
            <div class="col-6 mb-3">
              <label class="form-label">No. Kwitansi Bank</label>
              <input type="text" name="kwitansi_bank" class="form-control" />
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Nama Bank</label>
            <input type="text" name="nm_bank" class="form-control" />
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
<script>
  (function () {
    var ktgSelect = document.getElementById('ktgHimpun');
    var subKtgSelect = document.getElementById('subKtgHimpun');
    if (!ktgSelect || !subKtgSelect) return;

    var subOptions = Array.prototype.slice.call(subKtgSelect.querySelectorAll('option[data-ktg]'));

    ktgSelect.addEventListener('change', function () {
      var selectedKtg = this.value;
      subKtgSelect.value = '';
      subOptions.forEach(function (opt) {
        var matches = opt.getAttribute('data-ktg') === selectedKtg;
        opt.hidden = !matches;
      });
    });
  })();
</script>
<?= $this->endSection() ?>
