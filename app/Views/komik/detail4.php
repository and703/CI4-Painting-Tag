<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Painting Tag</title>
    <link rel="stylesheet" href="/assets/css/bootstrap.css">
    <link rel="stylesheet" href="/assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="stylesheet" href="/assets/css/pages/error.css">
    <link rel="shortcut icon" href="/assets/images/Pirelli_PZero.png" type="image/x-icon">
</head>

<body onload="doPrint(); return false;" style="background-color: #fff;">
  <div id="error">
    <script src="/js/qrcode.js"></script>
    <div class="error-page">
        <div class="row" align="left">
            <div class="col-md-12">
                <div class="text-center">
                    <p style="font-size: 300%; color: #000; font-weight:bold; margin-bottom: 0px;"><?= isset($komik['MAT_DESC']) ? $komik['MAT_DESC'] : '';?></p>
                    <p style="font-size: 400%; color: #000; font-weight:bold; margin-bottom: 0px;">IP:<?= isset($komik['MAT_IP_CODE']) ? $komik['MAT_IP_CODE'] : '';?>(<?= isset($komik2['Amount']) ? $komik2['Amount'] : '';?> Pcs) <?= isset($komik['MCH']) ? $komik['MCH'] : '';?></p>
                    <p style="font-size: 400%; color: #000; font-weight:bold; margin-bottom: 0px;">CURE MCH:<?= isset($komik['mch_cure']) ? $komik['mch_cure'] : '';?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-left">
                    <form id="reprint" action="/komik/update2/<?= isset($komik['id']) ? $komik['id'] : ''; ?>" method="post" enctype="multipart/form-data">
                    <?php
                        $count = (isset($komik['Count_Printed']) ? $komik['Count_Printed'] : 0)+1;
                    ?>
                        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                        <input type="hidden" name="Count_Printed" value="<?= $count; ?>">
                        <input type="hidden" name="mch" value="<?= isset($komik['On_Insert']) ? $komik['On_Insert'] : ''; ?>">
                        <input type="submit" style="position: absolute; left: -9999px; width: 1px; height: 1px;" tabindex="-1" />
                    </form>
                    <p style="font-size: 300%; color: #000; font-weight:bold; margin-bottom: 0px; text-decoration: underline;">OUTSIDE</p>
                    <p style="font-size: 250%; color: #000; font-weight:bold;margin-bottom: 0px;">Date Rel:<?= isset($komik['On_Insert']) ? $komik['On_Insert'] : '';?></p>
                    <p style="font-size: 250%; color: #000;margin-bottom: 0px;"><?= isset($komik['WM_NAME_WM_SURNAME']) ? $komik['WM_NAME_WM_SURNAME'] : '';?></p>
                    <p style="font-size: 250%; color: #000;margin-bottom: 0px;">GROUP: <?= isset($komik['WM_GROUP']) ? $komik['WM_GROUP'] : '';?></p>
                    <p style="font-size: 250%; color: #000;margin-bottom: 0px;">SHIFT: <?= isset($komik['WM_SHIFT']) ? $komik['WM_SHIFT'] : '';?></p>
                </div>
            </div>
            <div class="col-md-8">
                <div class="text-left">
                    <p style="font-size: 300%; color: #000; font-weight:bold; margin-bottom: 0px; text-decoration: underline;">INSIDE</p>
                    <p style="font-size: 250%; color: #000; font-weight:bold;margin-bottom: 0px;">Date Rel:<?= $komik2['On_Insert'];?></p>
                    <p style="font-size: 250%; color: #000;margin-bottom: 0px;"><?= $komik2['WM_NAME_WM_SURNAME'];?></p>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="text-left">
                    <p style="font-size: 250%; color: #000;margin-bottom: 0px;">GROUP: <?= $komik2['WM_GROUP'];?></p>
                    <p style="font-size: 250%; color: #000;margin-bottom: 0px;">SHIFT: <?= $komik2['WM_SHIFT'];?></p>
                    <p style="font-size: 450%; font-weight:bold; color: #000;margin-bottom: 0px;"><?= $komik2['Park'];?></p>
                </div>
            </div>
            <div class="col-sm-7">
                <div class="text-center">
                    <br>
                    <div id="qrcode2"></div>
					<div class="text-left">
						<p style="font-size: 300%; color: #000;margin-bottom: 0px;"><?= $komik2['id'];?></p>
					</div>
                    <?php
                        $Qr2 = $komik2['MAT_IP_CODE'].','.$komik2['Amount'].','.$komik2['On_Insert'].','.$komik2['WM_CODE'].','.$komik2['id'];
                    ?>
                    <script type="text/javascript">
                    var qrcode = new QRCode(document.getElementById("qrcode2"), {
                        text: "<?= $Qr2?>",
                        width: 250,
                        height: 250,
                        colorDark : "#000000",
                        colorLight : "#ffffff",
                        correctLevel : QRCode.CorrectLevel.H
                    });
                    </script>
                    <br>
                </div>
            </div>
            <div class="col-md-12">
                <div class="text-center">
                    <p style="font-size: 250%; color: #000; font-weight:bold;margin-bottom: 0px;">CURE AFTER : <?= isset($komik['CURE_TIME']) ? $komik['CURE_TIME'] : '';?></p>
                    <?php
                        date_default_timezone_set("Asia/Bangkok");
                        $Date1 = isset($komik['On_Insert']) ? $komik['On_Insert'] : '';
                        $text1 = str_replace('/', '-', $Date1);
                        $text2 = str_replace('.', ':', $text1);
                        $date = new DateTime($text2);
                        $date->add(new DateInterval('P5D')); // P1D means a period of 1 day
                        $Date2 = $date->format('d/m/Y H.i');
                    ?>
                    <p style="font-size: 250%; color: #000; font-weight:bold;margin-bottom: 0px;">Expired AFTER : <?= $Date2;?></p>
                    <br>
                    <br>
                    <p style="font-size: 300%; color: #000; margin-bottom: 0px; text-align:center;">TROLLEY NUMBER</p>
                    <p style="font-size:600%; color: #000; font-weight:bold; margin-bottom: 0px; text-align:center;"><?= isset($komik['tr_code']) ? $komik['tr_code'] : '';?></p>
                </div>
            </div>
        </div>
    </div>
  </div>
    
</body>

</html>

<script>
  function parseDMY_HM(str) {
    if (!str) return null;
    const s = String(str).trim();
    const m = s.match(/^(\d{1,2})[\/-](\d{1,2})[\/-](\d{4})\s+(\d{1,2})[.:](\d{1,2})$/);
    if (!m) return null;
    const dd = parseInt(m[1], 10), mm = parseInt(m[2], 10), yyyy = parseInt(m[3], 10);
    const HH = parseInt(m[4], 10), Min = parseInt(m[5], 10);
    return new Date(yyyy, mm - 1, dd, HH, Min, 0, 0);
  }

  // --- Values from PHP
  const onInsertRaw = "<?= isset($komik['On_Insert']) ? $komik['On_Insert'] : '';?>";
  const cureTimeRaw = "<?= isset($komik['CURE_TIME']) ? $komik['CURE_TIME'] : '';?>";

  const onInsertDT = parseDMY_HM(onInsertRaw);
  const cureDT     = parseDMY_HM(cureTimeRaw);

  if (!onInsertDT || !cureDT) {
    console.warn("Cannot parse datetime(s).", { onInsertRaw, cureTimeRaw });
  } else {
    const diffMs = cureDT - onInsertDT;
    const totalHours = diffMs / (1000 * 60 * 60); // gap in hours (decimal)

    console.log("Gap Hours:", totalHours.toFixed(2));

    if (totalHours >= 4) {
      console.log("OEM");
    } else {
      console.log("RE");
    }
  }
    function doPrint() {
        window.print();
        window.onafterprint = function(event) {
            document.getElementById("reprint").submit();
        };
    }
</script>