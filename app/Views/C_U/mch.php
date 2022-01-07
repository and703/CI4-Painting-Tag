<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="page-heading">
    <!-- validations start -->
    <section id="input-validation">
        <div class="row" align="left">
            <h5 class="card-title" style="font-size: 100%; color: #FFF;"><?= $worker->WM_NAME;?> <?= $worker->WM_SURNAME;?></h4>
            <h5 class="card-title" style="font-size: 100%; color: #FFF;"><?= $worker->WM_CODE;?></h4>
        </div>
        <div class="row" align="center">
            <div class="col-12">
                <div class="row">
                        <h5 class="card-title" style="font-size: 300%; color: #FFF017;">PAINTING MACHINE</h4>
                    <div class="col-6">
                        <h5 class="card-title" style="font-size: 300%; color: #FFF017;">A1</h4>
                        <h5 class="card-title" style="font-size: 200%; color: #FFF;">NumPad "1"</h4>
                    </div>
                    <div class="col-6">
                        <h5 class="card-title" style="font-size: 300%; color: #FFF017;">M1</h4>
                        <h5 class="card-title" style="font-size: 200%; color: #FFF;">NumPad "2"</h4>
                    </div>
                    <h5 class="card-title" style="font-size: 200%; color: #FFF017;">PRESS BUTTON TO CHOOSE</h4>
					<form id="mchForm" action="/worker/get_mch" method="post">
						<input type="hidden" name="WM_NAME_WM_SURNAME" value="<?= $worker->WM_NAME;?> <?= $worker->WM_SURNAME;?>">
						<input type="hidden" name="WM_CODE" value="<?= $worker->WM_CODE;?>">
						<input type="hidden" id="mch" name="mch">
						<input type="submit" style="position: absolute; left: -9999px; width: 1px; height: 1px;" tabindex="-1" />
					</form>
                </div>
            </div>
        </div>
    </section>
    <!-- validations end -->

    <script type="text/javascript"> 
        function Func() {
            document.getElementById("mchForm").submit();
        }

        let keysDown = {};
        window.onkeydown = function(e) {
            keysDown[e.key] = true;

            if (keysDown["1"]) {
                document.getElementById("mch").value = "A1"; //set value on myInputID
                //do what you want when control and a is pressed for example
                Func();
                console.log("1");
            }
            else if(keysDown["2"] ){
                document.getElementById("mch").value = "M1"; //set value on myInputID
                Func();
                console.log("2");
            }
        }

        window.onkeyup = function(e) {
        keysDown[e.key] = false;
        }
    </script>
</div>
<?= $this->endSection() ?>
