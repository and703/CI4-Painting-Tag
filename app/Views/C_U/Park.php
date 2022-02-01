<?php
    header("Refresh:600");
?>
<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<br>
<div class="page-heading">
    <!-- validations start -->
    <section id="input-validation" align="center">
        <div class="container">
            <div class="col-4">
                <div class="row">
                    <?php 
                        $mm = session()->get('logged_in_mm');
                        if(isset($mm)){
                            echo '<a href="worker/logout_MM" class="btn btn-danger" >Logout</a>';
                        }else{
                            echo '<a href="parking" class="btn btn-success" >Login</a>';
                        }
                    ?>
                    <h5 class="card-title" style="font-size: 100%; color: #FFF;"><?= session()->get('MM_CODE');?></h5>
                    <h5 class="card-title" style="font-size: 100%; color: #FFF;"><?= session()->get('MM_NAME')." ".session()->get('MM_SURNAME');?></h5>
                    <h5 class="card-title" style="font-size: 100%; color: #FFF;"><br></h5>
                </div>
            </div>
            <div id="h" class="row row-cols-12 row-cols-lg-10 g-1 g-lg-1">
            </div>
			<div class="row" align="center">
				<div class="col-12">
					<div class="row">
						<div class="row" align="left">
						</div>
						<h5 class="card-title" style="font-size: 200%; color: #FFF017;">SCAN TAG QRCODE</h4>
						<div class="col-12">
							<form action="/worker/get_tag" method="post" autocomplete="off">
                                <input style="text-align:center; font-weight:bold; width:70%" type="text" class="form-control" id="fill_ip" placeholder="Filter IP">
								<?php 
									$mm = session()->get('logged_in_mm');
									if(isset($mm)){
										echo '<input style="text-align:center; font-weight:bold; width:70%" type="text" class="form-control" name="listTag" placeholder="listTag" required autofocus>';
									}else{
										echo '';
									}
								?>
								<input type="submit" style="position: absolute; left: -9999px; width: 1px; height: 1px;" tabindex="-1" />
							</form>
						</div>
					</div>
				</div>
			</div>
        </div>
    </section>
    <!-- validations end -->

</div>
<script>

$(document).ready(function(){  
    const ip_code = document.getElementById('fill_ip');
    setInterval(function(){   
        $("#h").load("../co.php?ip="+ip_code.value);
    }, 500);
});
</script>
<?= $this->endSection() ?>
