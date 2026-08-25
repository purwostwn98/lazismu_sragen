<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<form action="<?= base_url('ajuan/store') ?>" method="post" enctype="multipart/form-data">
  <?= csrf_field() ?>

  <div class="card mb-4">
    <div class="card-header"><h5 class="mb-0">Data Ajuan</h5></div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Pemohon</label>
          <select name="nik" class="form-select" required>
            <option value="">-- Pilih Pemohon --</option>
            <?php foreach ($pemohon as $p): ?>
              <option value="<?= esc($p['nik']) ?>"><?= esc($p['nama_pemohon']) ?> (<?= esc($p['nik']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Pilih Pilar</label>
          <select id="pilarSelect" class="form-select" required>
            <option value="" disabled selected>-- Pilih Pilar --</option>
            <?php foreach ($pilar as $pl): ?>
              <option value="<?= $pl['id_pilar'] ?>"><?= esc($pl['nama_pilar']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Pilih Kategori Program</label>
          <select name="id_kategori_program" id="kategoriSelect" class="form-select" required>
            <option value="" disabled selected>-- Pilih pilar terlebih dahulu --</option>
          </select>
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Pilih Kegiatan</label>
          <select name="id_program" id="programSelect" class="form-select" required>
            <option value="" disabled selected>-- Pilih kategori terlebih dahulu --</option>
          </select>
        </div>
        <script type="application/json" id="programCascadeData"><?= json_encode(['kategori' => $kategori, 'program' => $program, 'syarat' => $syarat]) ?></script>
        <div class="col-md-6 mb-3">
          <label class="form-label">Nilai Diajukan (Rp)</label>
          <div class="input-group">
            <span class="input-group-text">Rp</span>
            <input type="text" inputmode="numeric" class="form-control" placeholder="0" data-currency-display="nilai_diajukan" required />
          </div>
          <input type="hidden" name="nilai_diajukan" />
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Jenis Ajuan</label>
          <div class="d-flex gap-4 mt-2">
            <div class="form-check">
              <input type="radio" name="jenis_ajuan" value="Individu" class="form-check-input" id="jenisIndividu" checked />
              <label class="form-check-label" for="jenisIndividu">Individu</label>
            </div>
            <div class="form-check">
              <input type="radio" name="jenis_ajuan" value="Lembaga" class="form-check-input" id="jenisLembaga" />
              <label class="form-check-label" for="jenisLembaga">Lembaga</label>
            </div>
          </div>
        </div>
        <div class="col-12 mb-3">
          <label class="form-label">Deskripsi Ajuan</label>
          <textarea name="deskripsi_ajuan" class="form-control" rows="3" required></textarea>
        </div>
        <div class="col-12 mb-3" id="templateFormulirBox" style="display: none;">
          <div class="alert alert-info mb-0">
            <div class="fw-semibold mb-2">Template Formulir Analisa</div>
            <ul id="templateFormulirList" class="mb-0"></ul>
            <div class="mt-2"><i>Download template di atas, isi, lalu unggah pada kolom Formulir di bawah ini.</i></div>
          </div>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Formulir (opsional)</label>
          <input type="file" name="file_formulir" class="form-control" />
        </div>
        <div class="col-12 mb-3" id="syaratProgramBox" style="display: none;">
          <div class="alert alert-info mb-0">
            <div class="fw-semibold mb-2">Syarat yang harus dilengkapi:</div>
            <ul id="syaratProgramList" class="mb-0"></ul>
            <div class="mt-2"><i>Gabungkan seluruh syarat di atas menjadi satu file (PDF) sesuai urutan, lalu unggah pada kolom Proposal di bawah ini.</i></div>
          </div>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Proposal <span class="text-danger">*</span></label>
          <input type="file" name="file_proposal" class="form-control" required />
          <small class="text-body-secondary">Wajib diunggah dan harus memuat seluruh syarat kegiatan yang dipilih.</small>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-4" id="blokIndividu">
    <div class="card-header"><h5 class="mb-0">Data Mustahik (Individu)</h5></div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-8 mb-3">
          <label class="form-label">NIK Mustahik</label>
          <div class="input-group">
            <input type="text" name="nik_mustahik" id="nikMustahik" class="form-control" maxlength="16" />
            <button type="button" class="btn btn-outline-primary" id="btnCekIndividu">Cek Data</button>
          </div>
          <small class="text-body-secondary">
            Isi NIK mustahik lalu klik "Cek Data" &mdash; jika pernah diajukan sebelumnya, data akan terisi otomatis.
          </small>
          <div id="individuCekResult" class="mt-2"></div>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label">Nama Mustahik</label>
          <input type="text" name="nama_mustahik" id="namaMustahik" class="form-control" />
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">Tempat Lahir</label>
          <input type="text" name="tempat_lahir_mustahik" id="tempatLahirMustahik" class="form-control" />
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">Tanggal Lahir</label>
          <input type="date" name="tgl_lahir_mustahik" id="tglLahirMustahik" class="form-control" />
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Jenis Kelamin</label>
          <select name="kelamin_mustahik" id="kelaminMustahik" class="form-select">
            <option value="Laki-laki">Laki-laki</option>
            <option value="Perempuan">Perempuan</option>
          </select>
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Agama</label>
          <select name="agama_mustahik" id="agamaMustahik" class="form-select">
            <?php foreach (['Islam', 'Protestan', 'Katolik', 'Hindhu', 'Budha'] as $ag): ?>
              <option value="<?= $ag ?>"><?= $ag ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Jumlah Keluarga</label>
          <input type="number" name="jml_keluarga" id="jmlKeluarga" class="form-control" min="0" />
        </div>

        <div class="row wilayah-block">
          <div class="col-md-6 mb-3">
            <label class="form-label">Provinsi</label>
            <select name="provinsi_mustahik" class="form-select sel-provinsi">
              <option value="">-- Pilih Provinsi --</option>
              <?php foreach ($provinsi as $p): ?>
                <option value="<?= $p['id_provinsi'] ?>"><?= esc($p['nama_provinsi']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Kabupaten/Kota</label>
            <select name="kabupaten_mustahik" class="form-select sel-kabupaten">
              <option value="">-- Pilih Provinsi dahulu --</option>
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Kecamatan</label>
            <select name="kecamatan_mustahik" class="form-select sel-kecamatan">
              <option value="">-- Pilih Kabupaten dahulu --</option>
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Kelurahan/Desa</label>
            <select name="kelurahan_mustahik" class="form-select sel-kelurahan">
              <option value="">-- Pilih Kecamatan dahulu --</option>
            </select>
          </div>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label">Dusun / Nama Jalan</label>
          <input type="text" name="dusun_mustahik" id="dusunMustahik" class="form-control" />
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">RT</label>
          <input type="number" name="rt_mustahik" id="rtMustahik" class="form-control" min="0" />
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">RW</label>
          <input type="number" name="rw_mustahik" id="rwMustahik" class="form-control" min="0" />
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Status Pendidikan</label>
          <select name="status_pendidikan" id="statusPendidikan" class="form-select">
            <?php foreach (['SD', 'SMP', 'SMA/SMK', 'Diploma', 'Sarjana', 'Pascasarjana', 'tidak tamat SD', 'lainnya'] as $sp): ?>
              <option value="<?= $sp ?>"><?= $sp ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Status Marital</label>
          <select name="status_marital" id="statusMarital" class="form-select">
            <?php foreach (['Lajang', 'Menikah', 'Cerai', 'Lainnya'] as $sm): ?>
              <option value="<?= $sm ?>"><?= $sm ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Pekerjaan</label>
          <select name="pekerjaan" id="pekerjaanMustahik" class="form-select">
            <option value="">-- Pilih --</option>
            <?php foreach ($pekerjaan as $pk): ?>
              <option value="<?= $pk['id_pekerjaan'] ?>"><?= esc($pk['nama_pekerjaan']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Penghasilan</label>
          <select name="penghasilan" id="penghasilanMustahik" class="form-select">
            <option value="">-- Pilih --</option>
            <?php foreach ($penghasilan as $pg): ?>
              <option value="<?= $pg['id_penghasilan'] ?>"><?= esc($pg['label_penghasilan']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">No. Handphone</label>
          <input type="text" name="no_handphone" id="noHandphoneMustahik" class="form-control" />
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email_mustahik" id="emailMustahik" class="form-control" />
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Nomor KK</label>
          <input type="text" name="kk" id="kkMustahik" class="form-control" />
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">Foto KTP</label>
          <input type="file" name="foto_ktp" class="form-control" />
          <small class="text-body-secondary">Kosongkan jika sudah pernah diunggah sebelumnya.</small>
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">Foto KK</label>
          <input type="file" name="foto_kk" class="form-control" />
          <small class="text-body-secondary">Kosongkan jika sudah pernah diunggah sebelumnya.</small>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-4 d-none" id="blokLembaga">
    <div class="card-header"><h5 class="mb-0">Data Lembaga</h5></div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-8 mb-3">
          <label class="form-label">Nomor Legalitas Lembaga (Akta/Izin Operasional/NIB)</label>
          <div class="input-group">
            <input type="text" name="nomor_lembaga" id="nomorLembaga" class="form-control" />
            <button type="button" class="btn btn-outline-primary" id="btnCekLembaga">Cek Data</button>
          </div>
          <small class="text-body-secondary">
            Isi nomor legalitas lalu klik "Cek Data" &mdash; jika lembaga sudah pernah terdaftar, data akan terisi
            otomatis.
          </small>
          <div id="lembagaCekResult" class="mt-2"></div>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label">Nama Lembaga</label>
          <input type="text" name="nama_lembaga" id="namaLembaga" class="form-control" />
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Bidang</label>
          <input type="text" name="bidang_lembaga" id="bidangLembaga" class="form-control" placeholder="mis. Pendidikan, Sosial, Dakwah" />
        </div>
        <div class="row wilayah-block">
          <div class="col-md-6 mb-3">
            <label class="form-label">Provinsi</label>
            <select name="provinsi_lembaga" class="form-select sel-provinsi">
              <option value="">-- Pilih Provinsi --</option>
              <?php foreach ($provinsi as $p): ?>
                <option value="<?= $p['id_provinsi'] ?>"><?= esc($p['nama_provinsi']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Kabupaten/Kota</label>
            <select name="kabupaten_lembaga" class="form-select sel-kabupaten">
              <option value="">-- Pilih Provinsi dahulu --</option>
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Kecamatan</label>
            <select name="kecamatan_lembaga" class="form-select sel-kecamatan">
              <option value="">-- Pilih Kabupaten dahulu --</option>
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Kelurahan/Desa</label>
            <select name="kelurahan_lembaga" class="form-select sel-kelurahan">
              <option value="">-- Pilih Kecamatan dahulu --</option>
            </select>
          </div>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label">Dusun / Nama Jalan</label>
          <input type="text" name="dusun_lembaga" id="dusunLembaga" class="form-control" />
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">RT</label>
          <input type="number" name="rt_lembaga" id="rtLembaga" class="form-control" min="0" />
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">RW</label>
          <input type="number" name="rw_lembaga" id="rwLembaga" class="form-control" min="0" />
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">Tahun Berdiri</label>
          <input type="number" name="tahun_berdiri_lembaga" id="tahunBerdiriLembaga" class="form-control" min="1900" max="<?= date('Y') ?>" />
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">NPWP</label>
          <input type="text" name="npwp_lembaga" id="npwpLembaga" class="form-control" />
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">Telepon</label>
          <input type="text" name="telepon_lembaga" id="teleponLembaga" class="form-control" />
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email_lembaga" id="emailLembaga" class="form-control" />
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Website</label>
          <input type="text" name="website_lembaga" id="websiteLembaga" class="form-control" />
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Nomor Rekening</label>
          <input type="text" name="nomor_rekening_lembaga" id="nomorRekeningLembaga" class="form-control" />
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Nama Penanggung Jawab</label>
          <input type="text" name="nama_pj_lembaga" id="namaPjLembaga" class="form-control" />
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Jabatan Penanggung Jawab</label>
          <input type="text" name="jabatan_pj_lembaga" id="jabatanPjLembaga" class="form-control" />
        </div>
        <div class="col-12 mb-3">
          <label class="form-label">Sumber Pendanaan</label>
          <input type="text" name="sumber_pendanaan_lembaga" id="sumberPendanaanLembaga" class="form-control" />
        </div>
      </div>
    </div>
  </div>

  <div class="d-flex gap-2 mb-4">
    <button type="submit" class="btn btn-primary">Ajukan</button>
    <a href="<?= base_url('ajuan') ?>" class="btn btn-label-secondary">Batal</a>
  </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script src="<?= base_url('assets/js/wilayah-cascade.js') ?>"></script>
<script src="<?= base_url('assets/js/program-cascade.js') ?>"></script>
<script src="<?= base_url('assets/js/currency-format.js') ?>"></script>
<script>
  (function () {
    var BASE = window.location.origin;

    window.ProgramCascade.init(document);
    window.CurrencyFormat.init(document);

    var jenisIndividu = document.getElementById('jenisIndividu');
    var jenisLembaga = document.getElementById('jenisLembaga');
    var blokIndividu = document.getElementById('blokIndividu');
    var blokLembaga = document.getElementById('blokLembaga');

    function toggleBlok() {
      if (jenisIndividu.checked) {
        blokIndividu.classList.remove('d-none');
        blokLembaga.classList.add('d-none');
      } else {
        blokIndividu.classList.add('d-none');
        blokLembaga.classList.remove('d-none');
      }
    }

    jenisIndividu.addEventListener('change', toggleBlok);
    jenisLembaga.addEventListener('change', toggleBlok);

    function setValue(id, value) {
      var el = document.getElementById(id);
      if (el) el.value = value || '';
    }

    function alertBox(kind, message) {
      return '<div class="alert alert-' + kind + ' py-2 mb-0">' + message + '</div>';
    }

    document.getElementById('btnCekIndividu').addEventListener('click', function () {
      var nik = document.getElementById('nikMustahik').value.trim();
      var resultEl = document.getElementById('individuCekResult');

      if (!nik) {
        resultEl.innerHTML = alertBox('warning', 'Isi NIK mustahik terlebih dahulu.');
        return;
      }

      var body = new URLSearchParams();
      body.append('nik_mustahik', nik);

      resultEl.innerHTML = alertBox('secondary', 'Memeriksa data&hellip;');

      fetch(BASE + '/ajuan/cek-individu', { method: 'POST', body: body })
        .then(function (res) { return res.json(); })
        .then(function (res) {
          if (!res.found) {
            resultEl.innerHTML = alertBox('info', 'Data belum terdaftar. Silakan lengkapi form di bawah ini.');
            return;
          }

          var d = res.data;
          setValue('namaMustahik', d.nama_mustahik);
          setValue('tempatLahirMustahik', d.tempat_lahir);
          setValue('tglLahirMustahik', d.tgl_lahir);
          setValue('kelaminMustahik', d.kelamin_mustahik);
          setValue('agamaMustahik', d.agama_mustahik);
          setValue('jmlKeluarga', d.jml_keluarga);
          setValue('dusunMustahik', d.dusun);
          setValue('rtMustahik', d.rt);
          setValue('rwMustahik', d.rw);
          setValue('statusPendidikan', d.status_pendidikan);
          setValue('statusMarital', d.status_marital);
          setValue('pekerjaanMustahik', d.pekerjaan);
          setValue('penghasilanMustahik', d.penghasilan);
          setValue('noHandphoneMustahik', d.no_handphone);
          setValue('emailMustahik', d.email);
          setValue('kkMustahik', d.kk);

          var block = document.querySelector('#blokIndividu .wilayah-block');
          if (block && window.WilayahCascade) {
            window.WilayahCascade.resolve(block, {
              provinsi: d.provinsi,
              kabupaten: d.kabupaten,
              kecamatan: d.kecamatan,
              kelurahan: d.desa
            });
          }

          resultEl.innerHTML = alertBox('success', 'Data mustahik ditemukan dan otomatis diisi. Silakan periksa kembali sebelum mengirim.');
        })
        .catch(function () {
          resultEl.innerHTML = alertBox('danger', 'Gagal memeriksa data. Coba lagi.');
        });
    });

    document.getElementById('btnCekLembaga').addEventListener('click', function () {
      var nomor = document.getElementById('nomorLembaga').value.trim();
      var resultEl = document.getElementById('lembagaCekResult');

      if (!nomor) {
        resultEl.innerHTML = alertBox('warning', 'Isi nomor legalitas lembaga terlebih dahulu.');
        return;
      }

      var body = new URLSearchParams();
      body.append('nomor_lembaga', nomor);

      resultEl.innerHTML = alertBox('secondary', 'Memeriksa data&hellip;');

      fetch(BASE + '/ajuan/cek-lembaga', { method: 'POST', body: body })
        .then(function (res) { return res.json(); })
        .then(function (res) {
          if (!res.found) {
            resultEl.innerHTML = alertBox('info', 'Lembaga belum terdaftar. Silakan lengkapi data di bawah ini.');
            return;
          }

          var d = res.data;
          setValue('namaLembaga', d.nama_lembaga);
          setValue('bidangLembaga', d.bidang);
          setValue('dusunLembaga', d.dusun);
          setValue('rtLembaga', d.rt);
          setValue('rwLembaga', d.rw);
          setValue('tahunBerdiriLembaga', d.tahun_berdiri);
          setValue('npwpLembaga', d.npwp);
          setValue('teleponLembaga', d.nomor_telepon);
          setValue('emailLembaga', d.email);
          setValue('websiteLembaga', d.website);
          setValue('nomorRekeningLembaga', d.nomor_rekening);
          setValue('namaPjLembaga', d.nama_pj);
          setValue('jabatanPjLembaga', d.jabatan_pj);
          setValue('sumberPendanaanLembaga', d.sumber_pendanaan);

          var lembagaBlock = document.querySelector('#blokLembaga .wilayah-block');
          if (lembagaBlock && window.WilayahCascade) {
            window.WilayahCascade.resolve(lembagaBlock, {
              provinsi: d.provinsi,
              kabupaten: d.kabupaten,
              kecamatan: d.kecamatan,
              kelurahan: d.desa
            });
          }

          resultEl.innerHTML = alertBox('success', 'Data lembaga ditemukan dan otomatis diisi. Silakan periksa kembali sebelum mengirim.');
        })
        .catch(function () {
          resultEl.innerHTML = alertBox('danger', 'Gagal memeriksa data. Coba lagi.');
        });
    });
  })();
</script>
<?= $this->endSection() ?>
