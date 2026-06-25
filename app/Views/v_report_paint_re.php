<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GT painting RePrint Report</title>

	<link rel="stylesheet" href="<?= base_url() ?>/template/plugins/fontawesome-free/css/all.min.css">
	<link rel="stylesheet" href="<?= base_url() ?>/template/datatables/bootstrap.min.css">

<script src="<?= base_url() ?>/template/plugins/jquery/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="<?= base_url() ?>/template/datatables/css.css" />
	<link rel="stylesheet" type="text/css" href="<?= base_url() ?>/sumfooter/css/jquery.dataTables.css" />

	<script type="text/javascript" src="<?= base_url() ?>/template/datatables/pdf.js"></script>
	<script type="text/javascript" src="<?= base_url() ?>/template/datatables/font.js"></script>
	<script type="text/javascript" src="<?= base_url() ?>/template/datatables/datatable.js"></script>
	<script type="text/javascript" src="<?= base_url() ?>/sumfooter/js/dataTables.colReorder.min.js"></script>

	<link href="<?= base_url() ?>/template/date/daterangepicker.css" rel="stylesheet">
	<script type="text/javascript" src="<?= base_url() ?>/template/date/date_moment.js"></script>
	<script type="text/javascript" src="<?= base_url() ?>/template/date/date_range.js"></script>

	<script src="<?= base_url() ?>/template/blokui.js"></script>
	<script src="<?= base_url() ?>/template/loader.js"></script>

</head>

<body>
	<div class="container">

		<div class="row">

			<div class="container mt-5">
				<div class="row">
					<div class="col-md-12">
						<div class="card">
							<div class="card-header">
								GT Painting RePrint Report
							</div>
							<div class="card-body">

								<div class="row clearfix">
									<div class="col-sm-4">
										<label>Date Range</label>
										<input required type="text" id="f1" name="periode" class="cursor form-control" onchange="reload_table()">
									</div>
									<div class="col-sm-4">
										<label>SHIFT</label>
										<select class="form-control show-tick" id="f2" data-live-search="true" onchange="reload_table()">
											<option value="">=== Pilih ===</option>
											<option value="1">1</option>
											<option value="2">2</option>
											<option value="3">3</option>
										</select>
									</div>
								</div>
								<hr>

								<div id='area_lod'>
									<table id="report1" class="table table-striped table-bordered table-hover">
										<thead>
											<tr>
												<td>No</td>
												<td>WM Code</td>
												<td>Machine</td>
												<td>IP Code</td>
												<td>Material Deskripsi</td>
												<td>Jumlah</td>
												<td>Slot</td>
												<td>On Insert</td>
												<td>Cure Time</td>
												<td>Count Printed</td>
												<td>Operator</td>
												<td>Group</td>
												<td>Shift</td>
												<td>Re</td>
											</tr>
										</thead>

										<tbody>
										</tbody>

										<tfoot>
											<tr>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td>Jumlah</td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
											</tr>
										</tfoot>
									</table>
								</div>

							</div>

						</div>
					</div>
				</div>
			</div>

	

			<script type="text/javascript">
				function rangetanggal() {
					$('#f1').daterangepicker({
						"showDropdowns": true,
						ranges: {
							'Today': [moment(), moment()],
							'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
							'1 week ago': [moment().subtract(7, 'days'), moment()],
						},
						"locale": {
							"format": "DD/MM/YYYY",
							"separator": " - ",
							"applyLabel": "Apply",
							"cancelLabel": "Cancel",
							"fromLabel": "From",
							"toLabel": "To",
							"customRangeLabel": "Customize",
							"weekLabel": "W",
							"daysOfWeek": [
								"Min",
								"Sen",
								"Sel",
								"Rab",
								"Kam",
								"Jum",
								"Sab",

							],
							"monthNames": [
								"Januari",
								"Februari",
								"Maret",
								"April",
								"Mei",
								"Juni",
								"Juli",
								"Agustus",
								"September",
								"Oktober",
								"November",
								"Desember"
							],
							"firstDay": 1
						},
						"startDate": moment(),
						"endDate": moment(),
						"opens": "left"
					}, function(start, end, label) {
						console.log('New date range selected: ' + start.format('DD/MM/YYYY') + ' to ' + end.format('DD/MM/YYYY') + ' (predefined range: ' + label + ')');
					});
				}
				rangetanggal();
				var dataTable = $('#report1').DataTable({
					"paging": true,
					"processing": false,
					"language": {
						"sSearch": "Search Barcode",
						"processing": ' <span class="sr-only dataTables_processing">Loading...</span> <br><b style="color:black;background:white">Process of displaying data<br> Please wait..</b>',
						"oPaginate": {
							"sFirst": "Page First",
							"sLast": "Page Last",
							"sNext": "Next",
							"sPrevious": "Previous"
						},
						"sInfo": "Total :  _TOTAL_ , Row (_START_ - _END_)",
						"sInfoEmpty": "No data displayed",
						"sZeroRecords": "Data not available",
						"lengthMenu": "&nbsp;Show _MENU_ entries",
					},
					"serverSide": true,
					"responsive": false,
					"searching": true,
					dom: 'Blfrtip',
					buttons: [{
							text: '<span class="fas fa-sync-alt"></span> Refresh',
							className: "btn btn-light btn-sm",
							action: function(e, dt, node, config) {
								reload_table();
							}
						},
						{
							extend: 'excelHtml5',
							exportOptions: {
								columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]
							},
							footer: true,
							text: '<span class="fas fa-file-excel"></span> Download Excell',
							className: "btn btn-light btn-sm",
							title: 'Painting RePrint',
							messageTop: 'DI CETAK : <?php echo date("d/m/Y H:i") ?>',
							filename: 'painting_reprint_report',
							customize: function(xlsx) {
								var source = xlsx.xl['workbook.xml'].getElementsByTagName('sheet')[0];
								source.setAttribute('name', 'painting_reprint');
							}
						},
					],
					"order": [
						[10, "desc"]
					],
					"ajax": {
						"url": "<?= site_url('report_paint_re/data_tables'); ?>",
						"type": "POST",
						"data": function(data) {
							data.f1 = $('#f1').val();
							data.f2 = $('#f2').val();
						},
						beforeSend: function() {
							loading('area_lod');
						},
						complete: function(data) {
							unblock('area_lod');
						},
					},
					"fixedHeader": true,
					"colReorder": true,
					"pageLength": 5,
					"lengthMenu": [
						[10, 25, 50, -1],
						[10, 25, 50, "All"]
					],

					"footerCallback": function(row, data, start, end, display) {
						var api = this.api(),
							data;

						var intVal = function(i) {
							return typeof i === 'string' ?
								i.replace(/[\,]/g, '') * 1 :
								typeof i === 'number' ?
								i : 0;
						};

						total = api
							.column(api.colReorder.transpose(5))
							.data()
							.reduce(function(a, b) {
								return intVal(a) + intVal(b);
							}, 0);

						pageTotal = api
							.column(api.colReorder.transpose(5), {
								page: 'current'
							})
							.data()
							.reduce(function(a, b) {
								return intVal(a) + intVal(b);
							}, 0);

						$(api.column(api.colReorder.transpose(5)).footer()).html(
							+ pageTotal + '   Total'
						);
					},

					"columnDefs": [{
						"targets": [-1, -2, -3, -4, -6, -7],
						"orderable": false,
					}, ],

				});

				function reload_table() {
					if (typeof dataTable !== 'undefined') {
						dataTable.ajax.reload(null, false);
					}
				}
			</script>

</body>

</html>
