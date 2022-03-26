<?php
//SQLSERVER CON
$serverName = "172.21.202.142"; 
$uid = "Traceability";   
$pwd = "ability";  
$databaseName = "PCS"; 

//MYSQL CON
$server = "localhost";
$username = "root";
$password = "";
$dbname = "SQL_PCS";

$tableName = "dc_containment_units";
$connectionInfo = array( "UID"=>$uid,                            
                         "PWD"=>$pwd,                            
                         "Database"=>$databaseName); 

/* Connect using SQL Server Authentication. */  
$conn = sqlsrv_connect( $serverName, $connectionInfo);  

$tsql = "SELECT
        SO_CODE, CU_EXT_PROGR, PP_CODE,
        SAP_WH_CODE, CNT_CODE, MAT_SAP_CODE,
        WM_CODE, MAT_VARIANT, PW_CODE, WH_CODE,
        LCN_ROW, LCN_COL, MCH_CODE, CU_SEQ_IN, CU_PHYSICAL_CODE, CU_SEQ_OUT,
        convert(varchar, CU_PRODUCTION_DATE, 120) AS CU_PRODUCTION_DATE,
        CU_GROUP, CU_GOOD_QTY, CU_SCRAP_QTY, CU_REAL_QTY, CU_BLOCK_TYPE,
        CU_FL_QUALITY, CU_PIECES, CU_TYPE, CU_PRODUCTION_ORDER,
        CU_CLIENT_REMARKS, CU_QUALITY_REMARKS,
        convert(varchar, CU_RESERVED, 120) AS CU_RESERVED,
        CU_TOT_CALC_QTY, CU_TOT_CALC_QTY_SS, CU_FL_INV,
        CU_LABEL_NOTIFIED, CU_ADD_STORAGE_DAYS, CU_ADD_STORAGE_REMARK,
        CU_WORKSTATION, WRU_CODE, CU_REMANING_QTY, CU_LOT,
        convert(varchar, CU_PRINTING_DATE, 120) AS CU_PRINTING_DATE,
        CU_PLANT_CODE, CU_ARRIVAL_DATE, CU_FIFO_RESULT, CU_SUB_STORAGE_HOURS, CU_SUB_STORAGE_REMARK,
        convert(varchar, CU_MIN_EXPIRATION_DATE, 120) AS CU_MIN_EXPIRATION_DATE,
        convert(varchar, CU_MAX_EXPIRATION_DATE, 120) AS CU_MAX_EXPIRATION_DATE,
        CU_PRODUCING_BU, CU_SENT_TO_MAGMA, CU_RECYCLE_TYPE, CU_OPTIONAL_INSTRUCTION,
        CU_BARCODE_LIST, CST_DESC 
        FROM PCS.dbo.DC_CONTAINMENT_UNITS
        WHERE CU_PRODUCTION_DATE >= CAST( GETDATE() AS Date )
        ORDER BY CU_PRODUCTION_DATE DESC";  

/* Execute the query. */  
$stmt = sqlsrv_query( $conn, $tsql );
if( $stmt === false) {
    die( print_r( sqlsrv_errors(), true) );
}

$con = new mysqli($server, $username, $password, $dbname);

while($row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)) {
    // Check connection
    if ($con->connect_error) {
      die("Connection failed: " . $con->connect_error);
    }
    $columns = implode(", ",array_keys($row));
    foreach ($row as $idx=>$data) $row[$idx] = "'".$data."'";
    $values  = implode(", ", $row);
    $query = "INSERT IGNORE INTO $tableName ($columns) VALUES ($values)";
    if ($con->query($query) === TRUE) {
    } else {
      echo "Error: " . $query . "<br>" . $con->error;
    }
}
echo 'Added : ' . mysqli_affected_rows($con);
$con->close();
?>