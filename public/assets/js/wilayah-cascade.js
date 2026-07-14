/**
 * Wires up cascading provinsi/kabupaten/kecamatan/kelurahan selects.
 * Any block of 4 selects marked with class "wilayah-block" (containing
 * .sel-provinsi/.sel-kabupaten/.sel-kecamatan/.sel-kelurahan) gets change
 * handlers that fetch the next level from /wilayah/{level}/{parentId}.
 *
 * Edit modals (wrapped in .modal-pemohon, .modal-ajuan, or .modal-mustahik) get their existing
 * selections lazily resolved the first time the modal is shown, using
 * data-selected-kabupaten/kecamatan/kelurahan on the .wilayah-block.
 *
 * window.WilayahCascade.resolve(block, { provinsi, kabupaten, kecamatan, kelurahan })
 * is exposed so other scripts (e.g. "check master data" prefill flows) can
 * resolve+select a whole chain programmatically without duplicating the
 * sequential fetch logic.
 */
(function () {
  var BASE = window.location.origin;

  function loadOptions(url, selectEl, valueKey, labelKey, selectedValue, placeholder) {
    selectEl.innerHTML = '<option value="">Memuat...</option>';
    return fetch(url)
      .then(function (res) { return res.json(); })
      .then(function (rows) {
        var html = '<option value="">' + placeholder + '</option>';
        rows.forEach(function (row) {
          var sel = selectedValue && String(row[valueKey]) === String(selectedValue) ? 'selected' : '';
          html += '<option value="' + row[valueKey] + '" ' + sel + '>' + row[labelKey] + '</option>';
        });
        selectEl.innerHTML = html;
      });
  }

  function wireBlock(block) {
    var provinsi = block.querySelector('.sel-provinsi');
    var kabupaten = block.querySelector('.sel-kabupaten');
    var kecamatan = block.querySelector('.sel-kecamatan');
    var kelurahan = block.querySelector('.sel-kelurahan');

    provinsi.addEventListener('change', function () {
      kecamatan.innerHTML = '<option value="">-- Pilih Kabupaten dahulu --</option>';
      kelurahan.innerHTML = '<option value="">-- Pilih Kecamatan dahulu --</option>';
      if (!this.value) {
        kabupaten.innerHTML = '<option value="">-- Pilih Provinsi dahulu --</option>';
        return;
      }
      loadOptions(BASE + '/wilayah/kabupaten/' + this.value, kabupaten, 'id_kabupaten', 'nama_kabupaten', null, '-- Pilih Kabupaten --');
    });

    kabupaten.addEventListener('change', function () {
      kelurahan.innerHTML = '<option value="">-- Pilih Kecamatan dahulu --</option>';
      if (!this.value) {
        kecamatan.innerHTML = '<option value="">-- Pilih Kabupaten dahulu --</option>';
        return;
      }
      loadOptions(BASE + '/wilayah/kecamatan/' + this.value, kecamatan, 'id_kecamatan', 'nama_kecamatan', null, '-- Pilih Kecamatan --');
    });

    kecamatan.addEventListener('change', function () {
      if (!this.value) {
        kelurahan.innerHTML = '<option value="">-- Pilih Kecamatan dahulu --</option>';
        return;
      }
      loadOptions(BASE + '/wilayah/kelurahan/' + this.value, kelurahan, 'id_kelurahan', 'nama_kelurahan', null, '-- Pilih Kelurahan --');
    });
  }

  /**
   * Selects provinsi immediately, then resolves kabupaten/kecamatan/kelurahan
   * in sequence, selecting each target id as its options arrive.
   */
  function resolve(block, selected) {
    selected = selected || {};
    var provinsi = block.querySelector('.sel-provinsi');
    var kabupaten = block.querySelector('.sel-kabupaten');
    var kecamatan = block.querySelector('.sel-kecamatan');
    var kelurahan = block.querySelector('.sel-kelurahan');

    if (!selected.provinsi) {
      return Promise.resolve();
    }

    provinsi.value = selected.provinsi;

    return loadOptions(BASE + '/wilayah/kabupaten/' + selected.provinsi, kabupaten, 'id_kabupaten', 'nama_kabupaten', selected.kabupaten, '-- Pilih Kabupaten --')
      .then(function () {
        if (!selected.kabupaten) return;
        return loadOptions(BASE + '/wilayah/kecamatan/' + selected.kabupaten, kecamatan, 'id_kecamatan', 'nama_kecamatan', selected.kecamatan, '-- Pilih Kecamatan --');
      })
      .then(function () {
        if (!selected.kecamatan) return;
        return loadOptions(BASE + '/wilayah/kelurahan/' + selected.kecamatan, kelurahan, 'id_kelurahan', 'nama_kelurahan', selected.kelurahan, '-- Pilih Kelurahan --');
      });
  }

  window.WilayahCascade = { resolve: resolve };

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.wilayah-block').forEach(wireBlock);
  });

  document.addEventListener('show.bs.modal', function (e) {
    var modal = e.target;
    if (!(modal.classList.contains('modal-pemohon') || modal.classList.contains('modal-ajuan') || modal.classList.contains('modal-mustahik')) || modal.dataset.wilayahLoaded) {
      return;
    }
    modal.dataset.wilayahLoaded = '1';

    modal.querySelectorAll('.wilayah-block').forEach(function (block) {
      var provinsi = block.querySelector('.sel-provinsi');
      if (!provinsi.value) return;

      resolve(block, {
        provinsi: provinsi.value,
        kabupaten: block.dataset.selectedKabupaten,
        kecamatan: block.dataset.selectedKecamatan,
        kelurahan: block.dataset.selectedKelurahan,
      });
    });
  });
})();
