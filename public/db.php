<?PHP
function insertArr($tableName, $insData){
    $server = "localhost";
    $username = "root";
    $password = "";
    $dbname = "SQL_PCS";
    
    $con = new mysqli($server, $username, $password, $dbname);
    // Check connection
    if ($con->connect_error) {
      die("Connection failed: " . $con->connect_error);
    }
    $columns = implode(", ",array_keys($insData));
    foreach ($insData as $idx=>$data) $insData[$idx] = "'".$data."'";
    $values  = implode(", ", $insData);
    $query = "INSERT IGNORE INTO $tableName ($columns) VALUES ($values)";
    if ($con->query($query) === TRUE) {
    } else {
      echo "Error: " . $query . "<br>" . $con->error;
    }
    echo mysqli_affected_rows($con);
    $con->close();
}
?>