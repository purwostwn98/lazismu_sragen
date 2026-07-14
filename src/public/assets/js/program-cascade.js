/**
 * Tiered Pilar -> Kategori Program -> Program selection, mirroring the
 * cascading dropdowns in lazismu_reborn's formulir_ajuan.php. The full
 * kategori/program catalog (small, public reference data) is embedded as
 * JSON on the page, so filtering happens client-side without extra requests.
 */
(function () {
  var TEMPLATE_MAP = {
    1: { label: 'individu', file: 'rumah_individu_01.pdf' },
    2: { label: 'sekolah', file: 'sekolah_02.pdf' },
    3: { label: 'usaha', file: 'usaha_individu_03.pdf' },
    4: { label: 'masjid', file: 'masjid_04.pdf' },
  };

  function fillSelect(select, options, placeholder) {
    select.innerHTML = '';

    var ph = document.createElement('option');
    ph.value = '';
    ph.textContent = placeholder;
    ph.disabled = true;
    ph.selected = true;
    select.appendChild(ph);

    options.forEach(function (o) {
      var opt = document.createElement('option');
      opt.value = o.value;
      opt.textContent = o.label;
      select.appendChild(opt);
    });
  }

  function init(root) {
    root = root || document;

    var pilarSelect = root.querySelector('#pilarSelect');
    var kategoriSelect = root.querySelector('#kategoriSelect');
    var programSelect = root.querySelector('#programSelect');
    var dataEl = root.querySelector('#programCascadeData');

    if (!pilarSelect || !kategoriSelect || !programSelect || !dataEl) {
      return;
    }

    var data = JSON.parse(dataEl.textContent || '{}');
    var kategoriList = data.kategori || [];
    var programList = data.program || [];
    var syaratList = data.syarat || [];

    var syaratBox = root.querySelector('#syaratProgramBox');
    var syaratListEl = root.querySelector('#syaratProgramList');

    function renderSyarat() {
      if (!syaratBox || !syaratListEl) {
        return;
      }

      var idProgram = programSelect.value;

      if (!idProgram) {
        syaratBox.style.display = 'none';
        syaratListEl.innerHTML = '';
        return;
      }

      var filtered = syaratList.filter(function (s) {
        return String(s.id_program) === String(idProgram);
      });

      syaratListEl.innerHTML = '';

      if (!filtered.length) {
        var li = document.createElement('li');
        li.textContent = 'Tidak ada syarat khusus untuk kegiatan ini. Lampirkan proposal sesuai kebutuhan kegiatan.';
        syaratListEl.appendChild(li);
      } else {
        filtered.forEach(function (s) {
          var li = document.createElement('li');
          li.textContent = s.syarat_program;
          syaratListEl.appendChild(li);
        });
      }

      syaratBox.style.display = '';
    }

    var templateBox = root.querySelector('#templateFormulirBox');
    var templateListEl = root.querySelector('#templateFormulirList');

    function renderTemplateFormulir() {
      if (!templateBox || !templateListEl) {
        return;
      }

      var idProgram = programSelect.value;
      var program = programList.filter(function (p) {
        return String(p.id_program) === String(idProgram);
      })[0];

      if (!program) {
        templateBox.style.display = 'none';
        templateListEl.innerHTML = '';
        return;
      }

      var tpl = TEMPLATE_MAP[String(program.jenis_formulir)];

      templateListEl.innerHTML = '';
      var li = document.createElement('li');

      if (!tpl) {
        li.textContent = 'Template formulir tidak tersedia, mohon unggah dokumen data mengenai organisasi Anda.';
      } else {
        li.textContent = 'Download template formulir analisa ' + tpl.label + ': ';
        var a = document.createElement('a');
        a.href = '/template_formulir/' + tpl.file;
        a.target = '_blank';
        a.textContent = 'di sini';
        li.appendChild(a);
      }

      templateListEl.appendChild(li);
      templateBox.style.display = '';
    }

    function onPilarChange() {
      var idPilar = pilarSelect.value;
      var filtered = kategoriList.filter(function (k) {
        return String(k.id_pilar) === String(idPilar);
      });

      fillSelect(
        kategoriSelect,
        filtered.map(function (k) {
          return { value: k.id_kategori_program, label: k.nama_kategori };
        }),
        filtered.length ? '-- Pilih Kategori Program --' : 'Tidak ada kategori untuk pilar ini'
      );

      fillSelect(programSelect, [], '-- Pilih kategori terlebih dahulu --');
      renderSyarat();
      renderTemplateFormulir();
    }

    function onKategoriChange() {
      var idKategori = kategoriSelect.value;
      var filtered = programList.filter(function (p) {
        return String(p.id_kategori_program) === String(idKategori);
      });

      fillSelect(
        programSelect,
        filtered.map(function (p) {
          return { value: p.id_program, label: p.nama_program };
        }),
        filtered.length ? '-- Pilih Kegiatan --' : 'Tidak ada kegiatan untuk kategori ini'
      );
      renderSyarat();
      renderTemplateFormulir();
    }

    function onProgramChange() {
      renderSyarat();
      renderTemplateFormulir();
    }

    pilarSelect.addEventListener('change', onPilarChange);
    kategoriSelect.addEventListener('change', onKategoriChange);
    programSelect.addEventListener('change', onProgramChange);

    // Optional pre-fill for editing an already-selected program (e.g. the B3
    // modal), rather than a blank create form.
    var initial = data.initial || null;

    if (initial && initial.idPilar) {
      pilarSelect.value = String(initial.idPilar);
      onPilarChange();

      if (initial.idKategoriProgram) {
        kategoriSelect.value = String(initial.idKategoriProgram);
        onKategoriChange();

        if (initial.idProgram) {
          programSelect.value = String(initial.idProgram);
          onProgramChange();
        }
      }
    } else {
      fillSelect(kategoriSelect, [], '-- Pilih pilar terlebih dahulu --');
      fillSelect(programSelect, [], '-- Pilih kategori terlebih dahulu --');
    }
  }

  window.ProgramCascade = { init: init };
})();
