<?php
date_default_timezone_set('Asia/Jakarta');

define('HOST','localhost');
define('USER','root');
define('PASS','root');
define('DB1', 'pcs');

// Buat Koneksinya
$db1 = new mysqli(HOST, USER, PASS, DB1);
$conn = new mysqli(HOST, USER, PASS, DB1);

if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}
?>
