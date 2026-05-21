<?php
date_default_timezone_set('Asia/Jakarta');

//MYSQL CON1
define('HOST','localhost');
define('USER','root');
define('PASS','');
define('DB1', 'pcs');

//MYSQL CON2
define('HOST1','localhost');
define('USER1','root');
define('PASS1','');
define('DB2', 'pcs');

//SQLSERVER CON
$serverName = "172.21.202.240"; //'172.21.202.142',
$uid = "Traceability";   
$pwd = "ability";  
$databaseName = "PCS"; 
$connectionInfo = array( "UID"=>$uid,                            
						"PWD"=>$pwd,                            
						"Database"=>$databaseName);  

$db1 = new mysqli(HOST, USER, PASS, DB1);
$conn = new mysqli(HOST, USER, PASS, DB1);
$conn1 = sqlsrv_connect( $serverName, $connectionInfo); 
$conn2 = new mysqli(HOST1, USER1, PASS1, DB2);

if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}
if ($conn2->connect_error) {
	die("Connection failed: " . $conn2->connect_error);
}

?>
