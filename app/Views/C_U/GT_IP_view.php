<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="page-heading">
    <!-- validations start -->
    <section id="input-validation">
        <div class="row" align="left">
            <div class="col-1">
            </div>
            <div class="col-1">
                <div class="row">
                    <h5 class="card-title" style="font-size: 300%; color: #FFF;"><?= $mch;?></h4>
                </div>
            </div>
            <div class="col-4">
                <div class="row">
                    <h5 class="card-title" style="font-size: 100%; color: #FFF;"><?= $worker->WM_CODE;?></h4>
                    <h5 class="card-title" style="font-size: 100%; color: #FFF;"><?= $worker->WM_NAME;?> <?= $worker->WM_SURNAME;?></h4>
                        <h5 class="card-title" style="font-size: 100%; color: #FFF;"><br></h4>
                </div>
            </div>
        </div>
        <div class="row" align="center">
            <div class="col-12">
                <div class="row">
                    <h5 class="card-title" style="font-size: 300%; color: #FFF017;">INPUT GT IPCODE</h4>
                    <div class="col-12">
					<form action="/worker/get_ip" method="post">
						<input type="hidden" name="mch" value="<?= $mch;?>">
						<input type="hidden" name="WM_CODE" value="<?= $worker->WM_CODE;?>">
                        <input style="text-align:center; font-weight:bold; width:50%" type="text" class="form-control" id="valid-state" name="MAT_IP_CODE" placeholder="MAT_IP_CODE" autofocus required>
						<input type="submit" style="position: absolute; left: -9999px; width: 1px; height: 1px;" tabindex="-1" />
					</form>
                    </div>
                    <h5 class="card-title" style="font-size: 200%; color: #FFF017;">PLEASE INPUT GT IPCODE</h4>
                </div>
            </div>
        </div>
    </section>
    <!-- validations end -->

</div>
<?= $this->endSection() ?>
