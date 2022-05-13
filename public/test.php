<?php
date_default_timezone_set("Asia/Bangkok");
$Date0 = "27/04/2022 08.15";
$txt = str_replace('/', '-', $Date0);
$txt2 = str_replace('.', ':', $txt);
$dt = new DateTime($txt2);
$dt->add(new DateInterval('P5D')); // P1D means a period of 1 day
$Date = $dt->format('d/m/Y H.i');
echo $Date;