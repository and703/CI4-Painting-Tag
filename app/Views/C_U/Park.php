<?= $this->extend('layouts/app') ?>
<?php
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
?>
<?= $this->section('content') ?>
<div class="page-heading">
    <!-- validations start -->
    <section id="input-validation" align="center">
        <div class="container">
            <div class="row row-cols-5 row-cols-lg-10 g-1 g-lg-2">
            <?php if($park): ?>
            <?php foreach($park as $p): ?>
                <div class="col">
                    <div class="card mb-4 rounded-3 shadow-sm">
                        <div class="card-header py-3">
                            <h4 class="my-0 fw-normal"><?= $p['slot'];?></h4>
                        </div>
                        <div class="card-body" style="
                            padding-right: 0px;
                            padding-bottom: 0px;
                            padding-left: 0px;">
                            <?php
                                if(!$p['id_paint'] == 0){
                                    $sql = "SELECT *  FROM painting WHERE id = ".$p['id_paint']." ORDER BY id ASC ";
                                    $result = $conn->query($sql);
                                    while($row = $result->fetch_assoc()) {
                            ?>
                                        <h1 class="card-title pricing-card-title"><?= $row['MAT_IP_CODE'];?></h1>
                                        <ul class="list-unstyled mt-3 mb-4">
                                            <li><?= $row['Amount'];?></li>
                                        </ul>
                            <?php
                                    }
                                }else{
                            ?>
                                        <h1 class="card-title pricing-card-title"></h1>
                                        <ul class="list-unstyled mt-3 mb-4">
                                            <li></li>
                                        </ul>
                            <?php
                                }
                            ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
    <!-- validations end -->

</div>
<?= $this->endSection() ?>
