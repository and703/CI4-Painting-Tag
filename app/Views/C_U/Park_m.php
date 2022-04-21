<?php
    header("Refresh:600");
?>
<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<br>
<div class="page-heading">
    <!-- validations start -->
    <section id="input-validation" align="center">
        <div class="">
            <div id="h" class="row row-cols-12 row-cols-xs-12 g-2 g-xs-2">
            </div>
        </div>
    </section>
    <!-- validations end -->

</div>
<script>

$(document).ready(function(){  
    setInterval(function(){   
        $("#h").load("../co.php?ip=");
    }, 500);
});
</script>
<?= $this->endSection() ?>