<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
	<div class="col-4">
		<div class="row">
			<h5 class="card-title" style="font-size: 100%; color: #FFF;"><?= session()->get('MM_CODE');?></h5>
			<h5 class="card-title" style="font-size: 100%; color: #FFF;"><?= session()->get('MM_NAME')." ".session()->get('MM_SURNAME');?></h5>
				<h5 class="card-title" style="font-size: 100%; color: #FFF;"><br></h4>
		</div>
	</div>
	<form id="reprint" action="/Worker/tagconf" method="post" enctype="multipart/form-data">
		<input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
		<input type="hidden" name="MM_CODE" value="<?= session()->get('MM_CODE'); ?>">
		<input type="hidden" name="id" value="<?= $painting['id']; ?>">
		<input type="hidden" name="Park" value="<?= $painting['Park']; ?>">
		<div class="page-heading">
							<br>
			<!-- validations start -->
			<section id="input-validation">
				<div class="row" align="center">
					<div class="col-md-12">
						<div class="text-center">
							<p style="font-size: 150%; color: #FFF017; margin-bottom: 0px;">SELECTED PARKING LOT</p>
							<p style="font-size: 250%; color: #00ff89; font-weight:bold; margin-bottom: 0px;"><?= $painting['Park'];?></p>
							<p style="font-size: 130%; color: #FFF017; margin-bottom: 0px;">SELECTED PAINTED GT TROLY</p>
							<p style="font-size: 250%; color: #00ff89; font-weight:bold; margin-bottom: 0px;"><?= $painting['MAT_IP_CODE'];?></p>
							<p style="font-size: 130%; color: #FFF017; margin-bottom: 0px;"><?= $painting['MAT_DESC'];?></p>
							<p style="font-size: 250%; color: #00ff89; font-weight:bold; margin-bottom: 0px;"><?= $painting['Amount'];?></p>
						</div>
					</div>
					<div class="col-md-12">
						<div class="text-left">
							<p style="font-size: 150%; color: #FFF017; margin-bottom: 0px;">CURE TIME</p>
							<p style="font-size: 250%; color: #00ff89; font-weight:bold; margin-bottom: 0px;"><?= $painting['CURE_TIME'];?></p>
							<br>
							<br>
							<input type="submit" class="btn btn-danger" style="font-size: 250%; color: #00ff89; font-weight:bold;" value="CONFIRM"/>
							<br>
							<br>
							<a class="btn btn-danger" href="javascript:history.back()" style="font-size: 250%; color: #00ff89; font-weight:bold;" >Go Back</a>
						</div>
					</div>
				</div>
			</section>
			<!-- validations end -->
		</div>
	</form>
<?= $this->endSection() ?>
