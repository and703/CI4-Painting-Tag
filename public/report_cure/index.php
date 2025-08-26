<?php
declare(strict_types=1);
$config = require __DIR__ . '/config.php';
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Advanced Report (Native PHP)</title>

  <!-- Bootstrap 5 -->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <!-- DataTables + Buttons (Bootstrap 5 integration) -->
  <link href="assets/css/datatables.min.css" rel="stylesheet"/>

  <style>
    body { padding: 20px; }
    .dt-buttons .btn { margin-right: .5rem; }
    .filter-card { position: sticky; top: 0; z-index: 1020; }
  </style>
</head>
<body>
<div class="container-fluid">

  <!-- Filters -->
  <div class="card shadow-sm mb-3 filter-card">
    <div class="card-body">
      <div class="row g-2 align-items-end">
        <div class="col-md-2">
          <label class="form-label">Date from</label>
          <input type="date" id="date_from" class="form-control">
        </div>
        <div class="col-md-2">
          <label class="form-label">Date to</label>
          <input type="date" id="date_to" class="form-control">
        </div>
		<div class="col-md-2">
		  <label class="form-label">Shift (dateAdj)</label>
		  <select id="shift_adj" class="form-select">
			<option value="">All shifts</option>
			<option value="1">Shift 1 (01:00–07:00)</option>
			<option value="2">Shift 2 (07:00–16:00)</option>
			<option value="3">Shift 3 (16:00–23:59)</option>
		  </select>
		</div>

        <div class="col-md-2">
          <label class="form-label">MAT_IP_CODE</label>
          <input type="text" id="sku" class="form-control" placeholder="MAT_IP_CODE contains...">
        </div>
        <div class="col-md-2">
          <label class="form-label">MM_CODE</label>
          <input type="text" id="barcode" class="form-control" placeholder="MM_CODE contains...">
        </div>
        <div class="col-md-2">
          <label class="form-label">Status</label>
          <input type="text" id="cured_stts" class="form-control" placeholder="Status contains...">
        </div>
        <div class="col-md-2">
          <label class="form-label">Park</label>
          <input type="text" id="Park" class="form-control" placeholder="Park contains...">
        </div>

        <div class="col-12 d-flex gap-2 mt-3">
          <button id="btnSearch" class="btn btn-primary">Search</button>
          <button id="btnReset" class="btn btn-outline-secondary">Reset</button>
          <a id="btnCsv" class="btn btn-success" href="#">Export CSV</a>
		  <a id="btnXlsx" class="btn btn-success" href="#">Export XLSX</a>

        </div>
      </div>
    </div>
  </div>

  <!-- Summary -->
  <div id="summary" class="row g-2 mb-3">
    <div class="col-md-4">
      <div class="alert alert-primary mb-0"><strong>Total Rows:</strong> <span id="sum_rows">-</span></div>
    </div>
    <div class="col-md-4">
      <div class="alert alert-secondary mb-0"><strong>Sum tag_stock:</strong> <span id="sum_tag_stock">-</span></div>
    </div>
    <div class="col-md-4">
      <div class="alert alert-secondary mb-0"><strong>Sum adj_stock:</strong> <span id="sum_adj_stock">-</span></div>
    </div>
  </div>

  <!-- Table -->
  <div class="card shadow-sm">
    <div class="card-body">
      <table id="reportTable" class="table table-striped table-bordered w-100">
        <thead><tr id="thead-row"></tr></thead>
        <tbody></tbody> <!-- explicit tbody -->
      </table>
    </div>
  </div>
</div>

<!-- JS -->
<script src="assets/js/jquery-3.7.1.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<!-- DataTables core (your existing combined bundle is fine) -->
<link  href="assets/css/datatables.min.css" rel="stylesheet">
<script src="assets/js/datatables.min.js"></script>

<!-- Buttons add-ons that the combo may not include -->
<link  href="assets/css/buttons.bootstrap5.min.css" rel="stylesheet">
<script src="assets/js/buttons.colVis.min.js"></script>
<script src="assets/js/buttons.print.min.js"></script>
<script>
let dt, inflight = false, debounceTimer = null;

/* Send the names PHP expects */
function currentFilters() {
  return {
    date_from:   $('#date_from').val() || '',
    date_to:     $('#date_to').val()   || '',
    MAT_IP_CODE: $('#sku').val()       || '',   // sku -> MAT_IP_CODE
    MM_CODE:     $('#barcode').val()   || '',   // barcode -> MM_CODE
    cured_stts:  $('#cured_stts').val()|| '',
    Park:        $('#Park').val()      || '',
    shift_adj:   $('#shift_adj').val() || ''
  };
}

/* Export links that mirror filters */
function buildExportUrl() {
  return './export/csv.php?' + new URLSearchParams(currentFilters()).toString();
}
function buildExportUrlXlsx() {
  return './export/xlsx.php?' + new URLSearchParams(currentFilters()).toString();
}
function updateExportLinks() {
  $('#btnCsv')?.attr('href', buildExportUrl());
  $('#btnXlsx')?.attr('href', buildExportUrlXlsx());
}

/* Read JSON from summary.php and write to the three spans */
function refreshSummary() {
  const qs = new URLSearchParams(currentFilters()).toString();
  $.getJSON('./api/summary.php?' + qs, function (s) {
    $('#sum_rows').text(s && Number(s.rows_count)    ? s.rows_count    : 0);
    $('#sum_tag_stock').text(s && Number(s.sum_tag_stock) ? s.sum_tag_stock : 0);
    $('#sum_adj_stock').text(s && Number(s.sum_adj_stock) ? s.sum_adj_stock : 0);
  }).fail(function (xhr) {
    console.error('summary.php failed:', xhr.status, xhr.responseText);
    $('#sum_rows, #sum_tag_stock, #sum_adj_stock').text(0);
  });
}

/* Guard against overlapping reloads */
function reloadTableSafe() { if (!inflight) dt.ajax.reload(null, false); }
function scheduleReload(ms=250){ clearTimeout(debounceTimer); debounceTimer = setTimeout(reloadTableSafe, ms); }

$(function () {
  // Build headers to match your aliases (optional — you already have them)
  const DT_COLUMNS = [
    { data:'id',                 title:'ID' },
    { data:'MM_CODE',            title:'Worker Code' },
    { data:'WM_NAME_WM_SURNAME', title:'Worker' },
    { data:'MAT_IP_CODE',        title:'Material IP' },
    { data:'MAT_DESC',           title:'Material Description' },
    { data:'id_paint',           title:'Paint ID' },
    { data:'Park',               title:'Park' },
    { data:'tag_stock',          title:'Tag Stock' },
    { data:'adj_stock',          title:'Adj Stock' },
    { data:'dateCURE',           title:'Date CURE' },
    { data:'cured_stts',         title:'Status' },
    { data:'dateAdj',            title:'Date Adj' },
  ];

  dt = $('#reportTable').DataTable({
    serverSide: true,
    processing: true,
    responsive: true,
    fixedHeader: true,
    deferRender: true,
    rowId: 'id',
    ajax: {
      url: './api/data.php',
      type: 'POST',
      data: d => Object.assign(d, currentFilters()),
      dataSrc: json => Array.isArray(json?.data) ? json.data : []
    },
    columns: DT_COLUMNS,
    order: [[0,'desc']],
    lengthMenu: [[10,25,50,100,250,500],[10,25,50,100,250,500]],
    dom: 'Bfrtip',
    buttons: [
      { extend: 'pageLength', text: 'Rows' },
      { extend: 'print', text: 'Print' }
    ]
  });

  // Single-flight + post-load updates
  $('#reportTable')
    .on('preXhr.dt', () => { inflight = true;  $('#btnSearch,#btnReset').prop('disabled', true); })
    .on('xhr.dt',    () => { inflight = false; $('#btnSearch,#btnReset').prop('disabled', false); refreshSummary(); updateExportLinks(); })
    .on('error.dt',  () => { inflight = false; $('#btnSearch,#btnReset').prop('disabled', false); });

  // Buttons
  $('#btnSearch').on('click', reloadTableSafe);
  $('#btnReset').on('click', () => {
    $('#date_from,#date_to,#sku,#barcode,#cured_stts,#Park,#shift_adj').val('');
    reloadTableSafe();
    refreshSummary();
    updateExportLinks();
  });

  // Debounce filter changes
  $('#date_from,#date_to,#sku,#barcode,#cured_stts,#Park,#shift_adj')
    .on('change input', () => scheduleReload(250));

  // Initial summary + links
  refreshSummary();
  updateExportLinks();
});
</script>

</body>
</html>
