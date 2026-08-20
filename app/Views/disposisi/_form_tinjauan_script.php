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
