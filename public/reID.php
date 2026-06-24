<?php
	include './conn.php';
	$s_keyword="";
	if (isset($_POST['id'])) {
		$s_keyword = $_POST['id'];
	}
	
	$no = 1;
	$query = "SELECT * FROM mman WHERE Paint_id =?";
	$dewan1 = $db1->prepare($query);
	$dewan1->bind_param('s', $s_keyword);
	$dewan1->execute();
	$res1 = $dewan1->get_result();

	if ($res1->num_rows > 0) {
		while ($row = $res1->fetch_assoc()) {
			echo '<h5 class="card-title" style="font-size: 400%; color: #FFF017;">Continue ReBoiacca</h4>';
		} 
	}else {
		echo '<h5 class="card-title" style="font-size: 400%; color: #FFF017;">Invalid ID</h4>';
	}
?>