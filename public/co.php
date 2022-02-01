<?php
date_default_timezone_set('Asia/Jakarta');
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "pcs";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
	}
	$sql1 = "SELECT *  FROM parking ORDER BY id ASC ";
	$result1 = $conn->query($sql1);
	while($row1 = $result1->fetch_assoc()) {
		if(! $_GET['ip'] == ''){
			$ip = $_GET['ip']; 
			if(!$row1['id_paint'] == 0){
				$sql = "SELECT *  FROM painting WHERE id = ".$row1['id_paint']." AND MAT_IP_CODE = ".$ip." ORDER BY id ASC ";
				$result = $conn->query($sql);
				while($row = $result->fetch_assoc()) {
					$dateCure = date_create_from_format('d/m/Y H.i', $row['CURE_TIME'])->format("Y-m-d H:i");
					$dateNow =  date_create()->format('Y-m-d H:i');
					if(strtotime($dateNow) <= strtotime($dateCure)){
?>
						<div class="col" >
							<div class="card rounded-pill shadow-sm">
								<div class="card-header py-1" style="background-color: #dc3545;;padding-left: 0px;"></div>
								<div class="card-body" style="background-color: #dc3545;">
									<h1 class="card-title pricing-card-title" style="font-size: 70%;padding-left: 0px;"><?= $row1['slot'];?></h1>
									<ul class="list-unstyled" style="margin-bottom: 0px;font-size: 70%;">
										<li><?= $row['MAT_IP_CODE'];?></li>
										<li><?= $row['Amount'];?></li>
									</ul>
								</div>
								<div class="card-footer py-1" style="background-color: #dc3545;"></div> 
							</div>
						</div>
<?php
					}else{
?>
						<div class="col" >
							<div class="card rounded-pill shadow-sm">
								<div class="card-header py-1" style="background-color: #ffc107;;padding-left: 0px;"></div>
								<div class="card-body" style="background-color: #ffc107;">
									<h1 class="card-title pricing-card-title" style="font-size: 70%;padding-left: 0px;"><?= $row1['slot'];?></h1>
									<ul class="list-unstyled" style="margin-bottom: 0px;font-size: 70%;">
										<li><?= $row['MAT_IP_CODE'];?></li>
										<li><?= $row['Amount'];?></li>
									</ul>
								</div>
								<div class="card-footer py-1" style="background-color: #ffc107;"></div>
							</div>
						</div>
<?php
					}
				}
			}
		}else{
			if(!$row1['id_paint'] == 0){
				$sql = "SELECT *  FROM painting WHERE id = ".$row1['id_paint']." ORDER BY id ASC ";
				$result = $conn->query($sql);
				while($row = $result->fetch_assoc()) {
					$dateCure = date_create_from_format('d/m/Y H.i', $row['CURE_TIME'])->format("Y-m-d H:i");
					$dateNow =  date_create()->format('Y-m-d H:i');
					if(strtotime($dateNow) <= strtotime($dateCure)){
?>
						<div class="col" >
							<div class="card rounded-pill shadow-sm">
								<div class="card-header py-1" style="background-color: #dc3545;;padding-left: 0px;"></div>
								<div class="card-body" style="background-color: #dc3545;">
									<h1 class="card-title pricing-card-title" style="font-size: 70%;padding-left: 0px;"><?= $row1['slot'];?></h1>
									<ul class="list-unstyled" style="margin-bottom: 0px;font-size: 70%;">
										<li><?= $row['MAT_IP_CODE'];?></li>
										<li><?= $row['Amount'];?></li>
									</ul>
								</div>
								<div class="card-footer py-1" style="background-color: #dc3545;"></div>
							</div>
						</div>
<?php
					}else{
?>
						<div class="col" >
							<div class="card rounded-pill shadow-sm">
								<div class="card-header py-1" style="background-color: #ffc107;;padding-left: 0px;"></div>
								<div class="card-body" style="background-color: #ffc107;">
									<h1 class="card-title pricing-card-title" style="font-size: 70%;padding-left: 0px;"><?= $row1['slot'];?></h1>
									<ul class="list-unstyled" style="margin-bottom: 0px;font-size: 70%;">
										<li><?= $row['MAT_IP_CODE'];?></li>
										<li><?= $row['Amount'];?></li>
									</ul>
								</div>
								<div class="card-footer py-1" style="background-color: #ffc107;"></div>
							</div>
						</div>
<?php
					}
				}
			}else{
?>
				<div class="col">
					<div class="card rounded-pill shadow-sm">
						<div class="card-header py-1" style="background-color: #34a11d;"></div>
						<div class="card-body" style="background-color: #34a11d;">
							<h1 class="card-title pricing-card-title" style="font-size: 70%;"><?= $row1['slot'];?></h1>
							<ul class="list-unstyled" style="margin-bottom: 42px;">
								<li></li>
							</ul>
						</div>
						<div class="card-footer py-1" style="background-color: #34a11d;"></div>
					</div>
				</div>
<?php
				}
		}
	}
?>