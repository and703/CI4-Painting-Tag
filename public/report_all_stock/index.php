<?php
declare(strict_types=1);
$config = require __DIR__ . '/config.php';
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Report Summarizer Per IP-CODE</title>

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
          <label class="form-label">MAT_IP_CODE</label>
          <input type="text" id="sku" class="form-control" placeholder="MAT_IP_CODE contains...">
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
      <div class="alert alert-secondary mb-0"><strong>Sum Total_Park:</strong> <span id="sum_Total_Amount">-</span></div>
    </div>
    <div class="col-md-4">
      <div class="alert alert-secondary mb-0"><strong>Sum Total_AdjStock:</strong> <span id="sum_Total_AdjStock">-</span></div>
    </div>
    <div class="col-md-4">
      <div class="alert alert-secondary mb-0"><strong>Sum Grand_TotalTotal:</strong> <span id="sum_Grand_Total">-</span></div>
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

<!-- DataTables Buttons HTML5 dependencies -->
<script src="assets/js/jszip.min.js"></script>
<script src="assets/js/pdfmake.min.js"></script>
<script src="assets/js/vfs_fonts.js"></script>
<script src="assets/js/buttons.html5.min.js"></script>


<!-- Buttons add-ons that the combo may not include -->
<link  href="assets/css/buttons.bootstrap5.min.css" rel="stylesheet">
<script src="assets/js/buttons.colVis.min.js"></script>
<script src="assets/js/buttons.print.min.js"></script>
<script>
let dt, inflight = false, debounceTimer = null;

/* Send the names PHP expects */
function currentFilters() {
  return {
    MAT_IP_CODE: $('#sku').val()       || ''
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
    const amount    = Number(s.sum_Total_Amount) || 0;
    const adjStock  = Number(s.sum_Total_AdjStock) || 0;
    $('#sum_Total_Amount').text(amount);
    $('#sum_Total_AdjStock').text(adjStock);
    $('#sum_Grand_Total').text(amount + adjStock);
  }).fail(function (xhr) {
    console.error('summary.php failed:', xhr.status, xhr.responseText);
    $('#sum_rows, #sum_Total_Amount, #sum_Total_AdjStock').text(0);
  });
}

/* Guard against overlapping reloads */
function reloadTableSafe() { if (!inflight) dt.ajax.reload(null, false); }
function scheduleReload(ms=250){ clearTimeout(debounceTimer); debounceTimer = setTimeout(reloadTableSafe, ms); }

$(function () {
  // Build headers to match your aliases (optional — you already have them)
  const DT_COLUMNS = [
    { data:'MAT_IP_CODE',        title:'Material IP' },
    { data:'Total_Amount',          title:'Park Stock' },
    { data:'Total_AdjStock',          title:'Adj Stock' },
    { data:'Grand_Total',           title:'Total Stock' },
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
    lengthMenu: [[10, 25, 50, 100, 250, 500, -1],[10,25,50,100,250,500,'All']],
    dom: 'Bfrtip',
	buttons: [
	  { extend: 'pageLength', text: 'Rows' },
	  
	  // Column visibility toggle
	  { extend: 'colvis', text: 'Columns' },

	  // Export only VISIBLE columns
		{
		  extend: 'copyHtml5',
		  text: 'Copy',
		  exportOptions: {
			columns: ':visible',
			header: false,      // don't export headers
			footer: false,      // don't export footers
			modifier: { search: 'applied', order: 'applied' }
		  },
		  title: '',            // prevent page <title> from being prepended
		  messageTop: '',       // no extra lines
		  messageBottom: '',
		  // Final safety net: strip any first line if it looks like a header/title
		  customize: function (data) {
			const lines = data.split('\n');

			// trim leading empties
			while (lines.length && !lines[0].trim()) lines.shift();

			// if first line looks like a header/title, drop it
			if (lines.length && /material\s*ip|park\s*stock|adj\s*stock|total\s*stock/i.test(lines[0])) {
			  lines.shift();
			}

			return lines.join('\n');
		  }
		},
	  { extend: 'csvHtml5',   text: 'CSV',   exportOptions: { columns: ':visible' } },
	  { extend: 'excelHtml5', text: 'Excel', exportOptions: { columns: ':visible' } },
	  { extend: 'pdfHtml5',   text: 'PDF',   exportOptions: { columns: ':visible' } },

	  // Print only visible columns
	  { extend: 'print', text: 'Print', exportOptions: { columns: ':visible' } }
	],
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
