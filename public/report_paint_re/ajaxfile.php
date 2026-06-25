<?php
error_reporting(0);
include 'config.php';
header('Content-Type: application/json');

if (!$con) {
    echo json_encode(["draw"=>intval($_POST['draw']??1),"iTotalRecords"=>0,"iTotalDisplayRecords"=>0,"aaData"=>[]]);
    die;
}

$draw = $_POST['draw']??1;
$row = $_POST['start']??0;
$rowperpage = $_POST['length']??10;
$columnIndex = $_POST['order'][0]['column']??0;
$columnSortOrder = $_POST['order'][0]['dir']??'desc';
$searchValue = $_POST['search']['value']??'';

$searchByFromdate = mysqli_real_escape_string($con, $_POST['searchByFromdate']??'');
$searchByTodate = mysqli_real_escape_string($con, $_POST['searchByTodate']??'');
$searchShift = mysqli_real_escape_string($con, $_POST['searchShift']??'');

// Map DataTable column data names to actual DB column names
$dbCols = ['id','WM_CODE','MCH','MAT_IP_CODE','MAT_DESC','Amount','Park','On_Insert','CURE_TIME','Count_Printed','WM_NAME_WM_SURNAME','WM_GROUP','WM_SHIFT','Re'];
$columnName = isset($dbCols[$columnIndex]) ? $dbCols[$columnIndex] : 'id';

$searchQuery = " ";
if ($searchValue != '') {
    $s = mysqli_real_escape_string($con, $searchValue);
    $searchQuery = " and ( WM_NAME_WM_SURNAME like '%$s%' or MAT_IP_CODE like '%$s%' or Park like '%$s%' or WM_CODE like '%$s%' ) ";
}

if ($searchByFromdate != '' && $searchByTodate != '') {
    $searchQuery .= " and (str_to_date(On_Insert,'%d/%m/%Y %H.%i') between '$searchByFromdate 00:00:00' and '$searchByTodate 23:59:59') ";
}

if ($searchShift != '') {
    $searchQuery .= " and (WM_SHIFT = '$searchShift') ";
}

$countAll = @mysqli_query($con, "select count(*) as allcount from painting_re");
$totalRecords = 0;
if ($countAll) {
    $r = @mysqli_fetch_assoc($countAll);
    $totalRecords = $r ? $r['allcount'] : 0;
}

$totalRecordwithFilter = $totalRecords;
$countFiltered = @mysqli_query($con, "select count(*) as allcount from painting_re WHERE 1 $searchQuery");
if ($countFiltered) {
    $r = @mysqli_fetch_assoc($countFiltered);
    $totalRecordwithFilter = $r ? $r['allcount'] : 0;
}

$data = array();
$empQuery = "select * from painting_re WHERE 1 $searchQuery order by $columnName $columnSortOrder limit $row,$rowperpage";
$empRecords = @mysqli_query($con, $empQuery);
$no = intval($row) + 1;

if ($empRecords) {
    while ($row = @mysqli_fetch_assoc($empRecords)) {
        if (!$row) break;
        $data[] = array(
            "NO" => $no++,
            "WM_CODE" => $row['WM_CODE']??'',
            "MCH" => $row['MCH']??'',
            "IP_CODE" => $row['MAT_IP_CODE']??$row['IP_CODE']??'',
            "MAT_DESC" => $row['MAT_DESC']??'',
            "AMOUNT" => $row['Amount']??$row['AMOUNT']??'',
            "SLOT" => $row['Park']??$row['SLOT']??'',
            "PRINT_OUT" => $row['On_Insert']??$row['PRINT_OUT']??'',
            "CURE_TIME" => $row['CURE_TIME']??$row['CURING_TIME']??'',
            "COUNT_PRINTED" => $row['Count_Printed']??$row['COUNT_PRINTED']??'',
            "USERNAME" => $row['WM_NAME_WM_SURNAME']??$row['USERNAME']??'',
            "GROUP_PAINT" => $row['WM_GROUP']??$row['GROUP_PAINT']??'',
            "SHIFT" => $row['WM_SHIFT']??$row['SHIFT']??'',
            "RE" => $row['Re']??$row['RE']??'',
        );
    }
}

echo json_encode(array(
    "draw" => intval($draw),
    "iTotalRecords" => $totalRecords,
    "iTotalDisplayRecords" => $totalRecordwithFilter,
    "aaData" => $data
));
die;
