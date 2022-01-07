<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Painting Tag</title>

<!--     <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet"> -->
    <link rel="stylesheet" href="/assets/css/bootstrap.css">

    <link rel="stylesheet" href="/assets/vendors/iconly/bold.css">

    <link rel="stylesheet" href="/assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="/assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/app.css">
    <?= $this->renderSection('styles') ?>
    <link rel="shortcut icon" href="/assets/images/Pirelli_PZero.png" type="image/x-icon">
    
    <script type="text/javascript"> 
        function display_c(){
        var refresh=1000; // Refresh rate in milli seconds
        mytime=setTimeout('display_ct()',refresh)
        }

        function display_ct() {
        var x = new Date()
        document.getElementById('ct').innerHTML = x;
        document.getElementById('ct').style.fontSize='20px';
        document.getElementById('ct').style.color='#FFF017';
        display_c();
        }
    </script>
</head>

<body onload=display_ct();>
    <div id="app">
        <!-- Main -->
        <div id="main" style="padding-top: 0px;padding-bottom: 0px;">
        
        <a href="/worker" id="0" style="font-size: 250%; color: #FFF017; font-weight:bold; margin-bottom: 0px;">Press "+" For Painting Tag</a>
            <footer>
                <div class="footer clearfix mb-0 text-muted">
                    <div class="float-end" style="padding-right: 10px;">
                        <span id='ct'></span>
                    </div>
                </div>
            </footer>
            <!-- Content -->
            <?= $this->renderSection('content') ?>
            <!-- End Content -->
            
            <!-- Footer -->
            <?= $this->include('layouts/footer') ?>
            <!-- End Footer -->
        </div>
        <!-- End Main -->
    </div>

    <script src="/assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>

    <?= $this->renderSection('javascript') ?>
</body>
</html>
