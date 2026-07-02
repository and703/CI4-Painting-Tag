<?php
	include './conn.php';
	$s_keyword="";
	if (isset($_POST['keyword'])) {
		$s_keyword = $_POST['keyword'];
	}

	if (strlen($s_keyword) < 2) {
		echo '';
		exit;
	}

	$tr_type = substr($s_keyword, 0, 1);
	$tr_num = intval(substr($s_keyword, 1));

	$qdet = "SELECT d.id, d.tr_cat, s.TR_NUM, d.tr_desc, s.TR_STATUS, s.TR_LOC
			FROM tr_status s
			INNER JOIN tr_det d ON d.id = s.TR_ID
			WHERE s.TR_NUM = ? AND d.tr_cat = ?";
	$st = $db1->prepare($qdet);
	if (!$st) {
		echo '';
		exit;
	}
	$st->bind_param('is', $tr_num, $tr_type);
	$st->execute();
	$res = $st->get_result();

	if ($res->num_rows > 0) {
		$row = $res->fetch_assoc();
		$cat_label = $row['tr_cat'] == '1' ? 'Outside' : ($row['tr_cat'] == '2' ? 'Inside' : 'Mixed');
		echo '<h5 class="card-title" style="font-size: 300%; color: #FFF017;">TR ' . $row['tr_desc'] . ' (' . $cat_label . ')</h4>';
		echo '<h5 class="card-title" style="font-size: 200%; color: #28a745;">Status: ' . ($row['TR_STATUS'] == 1 ? 'Available' : 'In Use') . ' - Loc: ' . $row['TR_LOC'] . '</h4>';
	} else {
		echo '<h5 class="card-title" style="font-size: 300%; color: #dc3545;">Invalid Trolley Code</h4>';
	}
?>
