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

	$query = "SELECT d.id, d.tr_cat, d.tr_desc FROM tr_det d WHERE d.tr_type = ? AND d.tr_num = ?";
	$dewan1 = $db1->prepare($query);
	if (!$dewan1) {
		echo '';
		exit;
	}
	$dewan1->bind_param('si', $tr_type, $tr_num);
	$dewan1->execute();
	$res1 = $dewan1->get_result();

	if ($res1->num_rows > 0) {
		$row = $res1->fetch_assoc();
		$det_id = $row['id'];

		$query2 = "SELECT s.TR_STATUS, s.TR_LOC FROM tr_status s WHERE s.TR_ID = ? AND s.TR_NUM = ?";
		$dewan2 = $db1->prepare($query2);
		if ($dewan2) {
			$dewan2->bind_param('ii', $det_id, $tr_num);
			$dewan2->execute();
			$res2 = $dewan2->get_result();

			if ($res2->num_rows > 0) {
				$srow = $res2->fetch_assoc();
				$cat_label = $row['tr_cat'] == '1' ? 'Outside' : ($row['tr_cat'] == '2' ? 'Inside' : 'Mixed');
				echo '<h5 class="card-title" style="font-size: 300%; color: #FFF017;">TR ' . $row['tr_desc'] . ' (' . $cat_label . ')</h4>';
				echo '<h5 class="card-title" style="font-size: 200%; color: #28a745;">Status: ' . ($srow['TR_STATUS'] == 1 ? 'Available' : 'In Use') . ' - Loc: ' . $srow['TR_LOC'] . '</h4>';
			} else {
				echo '<h5 class="card-title" style="font-size: 300%; color: #dc3545;">Trolley Not Found in Status</h4>';
			}
		} else {
			echo '<h5 class="card-title" style="font-size: 300%; color: #dc3545;">Trolley Not Found in Status</h4>';
		}
	} else {
		echo '<h5 class="card-title" style="font-size: 300%; color: #dc3545;">Invalid Trolley Code</h4>';
	}
?>
