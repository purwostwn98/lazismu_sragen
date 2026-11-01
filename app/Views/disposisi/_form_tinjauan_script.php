<?php
/**
 * Shared JS for disposisi/_form_tinjauan.php: Quill init, syncing its HTML
 * into the hidden textarea on submit, the rekomendasi->nominal toggle, and
 * the summary-card <-> form-card Edit/Batal toggle.
 *
 * Expects: $placeholder (string, optional).
 */
$placeholder = $placeholder ?? 'Tuliskan hasil tinjauan...';
?>
<script>
  (function () {
    var deskripsiEditor = new Quill('#deskripsiEditor', {
      theme: 'snow',
      placeholder: <?= json_encode($placeholder) ?>,
      modules: {
        toolbar: [
          ['bold', 'italic', 'underline', 'strike'],
          [{ list: 'ordered' }, { list: 'bullet' }],
          ['blockquote'],
          ['clean']
        ]
      }
    });

    var form = document.getElementById('reviewForm');
    form.addEventListener('submit', function () {
      document.getElementById('deskripsiInput').value = deskripsiEditor.root.innerHTML;
    });

    var nominalWrapper = document.getElementById('nominalWrapper');
    var nominalInput = document.getElementById('nominalInput');
    document.querySelectorAll('input[name="rekomendasi"]').forEach(function (radio) {
      radio.addEventListener('change', function () {
        var disetujui = document.getElementById('rekomendasiSetuju').checked;
        nominalWrapper.classList.toggle('d-none', !disetujui);
        nominalInput.required = disetujui;
      });
    });

    // ---- Dokumen lain: add/remove rows, max 5 in total (saved + new) ----
    var dokWrapper = document.getElementById('dokumenLainWrapper');
    if (dokWrapper) {
      var MAKS = parseInt(dokWrapper.dataset.maks, 10);
      var MAKS_UKURAN = parseInt(dokWrapper.dataset.maksUkuran, 10);
      var FORMAT = ['pdf', 'jpg', 'jpeg', 'png'];
      var baris = document.getElementById('dokumenLainBaris');
      var btnTambah = document.getElementById('btnTambahDokumen');
      var info = document.getElementById('infoDokumenLain');
      var infoAwal = info.textContent;

      function jumlahTersimpanAktif() {
        var semua = dokWrapper.querySelectorAll('.dokumen-tersimpan').length;
        var dihapus = dokWrapper.querySelectorAll('.cek-hapus-dokumen:checked').length;
        return semua - dihapus;
      }

      function segarkan() {
        var total = jumlahTersimpanAktif() + baris.children.length;
        btnTambah.disabled = total >= MAKS;
        info.textContent = total >= MAKS ? 'Sudah mencapai batas ' + MAKS + ' dokumen.' : infoAwal;
      }

      function tambahBaris() {
        if (jumlahTersimpanAktif() + baris.children.length >= MAKS) return;

        var row = document.createElement('div');
        row.className = 'row g-2 align-items-start mb-2 dokumen-baru';
        row.innerHTML =
          '<div class="col-md-5"><input type="file" name="dokumen_lain[]" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png" />' +
          '<div class="text-danger small d-none galat-dokumen"></div></div>' +
          '<div class="col-md-6"><input type="text" name="keterangan_lain[]" class="form-control form-control-sm" maxlength="255" placeholder="Keterangan dokumen (opsional)" /></div>' +
          '<div class="col-md-1"><button type="button" class="btn btn-sm btn-label-danger w-100 hapus-baris" title="Hapus baris"><i class="icon-base ti tabler-x"></i></button></div>';
        baris.appendChild(row);

        var input = row.querySelector('input[type="file"]');
        var galat = row.querySelector('.galat-dokumen');
        input.addEventListener('change', function () {
          galat.classList.add('d-none');
          var f = input.files[0];
          if (!f) return;
          var ext = f.name.split('.').pop().toLowerCase();
          var pesan = null;
          if (FORMAT.indexOf(ext) === -1) pesan = 'Format harus PDF, JPG, atau PNG.';
          else if (f.size > MAKS_UKURAN) pesan = 'Ukuran file melebihi 1 MB.';
          if (pesan) {
            input.value = '';
            galat.textContent = pesan;
            galat.classList.remove('d-none');
          }
        });
        row.querySelector('.hapus-baris').addEventListener('click', function () {
          row.remove();
          segarkan();
        });
        segarkan();
      }

      btnTambah.addEventListener('click', tambahBaris);
      dokWrapper.querySelectorAll('.cek-hapus-dokumen').forEach(function (cek) {
        cek.addEventListener('change', segarkan);
      });
      segarkan();
    }

    var summaryCard = document.getElementById('reviewSummaryCard');
    var formCard = document.getElementById('formulirReviewCard');
    var btnEdit = document.getElementById('btnEditReview');
    var btnBatal = document.getElementById('btnBatalEditReview');

    if (btnEdit) {
      btnEdit.addEventListener('click', function () {
        summaryCard.classList.add('d-none');
        formCard.classList.remove('d-none');
        formCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
      });
    }

    if (btnBatal) {
      btnBatal.addEventListener('click', function () {
        formCard.classList.add('d-none');
        summaryCard.classList.remove('d-none');
      });
    }
  })();
</script>
