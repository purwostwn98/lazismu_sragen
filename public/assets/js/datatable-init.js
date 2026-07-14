/**
 * Turns every table marked with class "datatable" into a DataTable,
 * stretched to fill its container (width: 100%, autoWidth off so it
 * doesn't shrink back to the sum of its column contents). Responsive
 * mode is intentionally off: columns stay laid out horizontally and the
 * surrounding .table-responsive wrapper scrolls sideways if needed,
 * instead of DataTables collapsing columns into an expandable accordion.
 */
(function ($) {
  if (typeof $ === 'undefined' || !$.fn.DataTable) {
    return;
  }

  $(function () {
    $('table.datatable').each(function () {
      if ($.fn.DataTable.isDataTable(this)) {
        return;
      }

      $(this).DataTable({
        width: '100%',
        autoWidth: false,
        language: {
          search: 'Cari:',
          lengthMenu: 'Tampilkan _MENU_ data',
          info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
          infoEmpty: 'Tidak ada data',
          emptyTable: 'Belum ada data.',
          zeroRecords: 'Data tidak ditemukan',
          paginate: {
            previous: '<i class="icon-base ti tabler-chevron-left icon-18px"></i>',
            next: '<i class="icon-base ti tabler-chevron-right icon-18px"></i>'
          }
        }
      });
    });
  });

  // Tables initialized while their Bootstrap tab-pane is hidden (display:none)
  // end up with collapsed/zero-width columns. Re-measure them once the tab
  // that contains them is actually shown.
  $(document).on('shown.bs.tab', '[data-bs-toggle="tab"]', function (e) {
    var target = $(e.target).attr('data-bs-target') || $(e.target).attr('href');
    if (!target) {
      return;
    }

    $(target)
      .find('table.datatable')
      .each(function () {
        if ($.fn.DataTable.isDataTable(this)) {
          $(this).DataTable().columns.adjust();
        }
      });
  });
})(typeof jQuery !== 'undefined' ? jQuery : undefined);
