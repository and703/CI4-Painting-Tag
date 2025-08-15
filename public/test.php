<?php
// Input from d/m/Y H.i -> Y-m-d H:i
$dateAdj = date_create_from_format('d/m/Y H.i', '06/08/2025 15.00')->format("Y-m-d H:i");


        $TAG = '12187,asd1kj2,djakj287,129742hjka,622189';
        $id_TAG = explode(",", $TAG);
echo $id_TAG[4];
