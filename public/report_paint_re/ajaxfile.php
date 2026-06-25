<?php
error_reporting(0);
include 'config.php';
header('Content-Type: application/json');

$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length'];
$columnIndex = $_POST['order'][0]['column'];
$columnName = $_POST['columns'][$columnIndex]['data'];
$columnSortOrder = $_POST['order'][0]['dir'];
$searchValue = $_POST['search']['value'];

$searchByFromdate = mysqli_real_escape_string($con, $_POST['searchByFromdate']);
$searchByTodate = mysqli_real_escape_string($con, $_POST['searchByTodate']);
$searchShift = mysqli_real_escape_string($con, $_POST['searchShift']);

$searchQuery = " ";
if ($searchValue != '') {
    $searchQuery = " and ( WM_NAME_WM_SURNAME like '%" . $searchValue . "%' or MAT_IP_CODE like '%" . $searchValue . "%' or Park like '%" . $searchValue . "%' or WM_CODE like '%" . $searchValue . "%' ) ";
}

if ($searchByFromdate != '' && $searchByTodate != '') {
    $searchQuery .= " and (str_to_date(On_Insert,'%d/%m/%Y %H.%i') between '" . $searchByFromdate . " 00:00:00' and '" . $searchByTodate . " 23:59:59') ";
}

if ($searchShift != '') {
    $searchQuery .= " and (WM_SHIFT = '" . $searchShift . "') ";
}

$countAll = mysqli_query($con, "select count(*) as allcount from painting_re");
if (!$countAll) { echo json_encode(["draw"=>intval($draw),"iTotalRecords"=>0,"iTotalDisplayRecords"=>0,"aaData"=>[]]); die; }
$totalRecords = mysqli_fetch_assoc($countAll)['allcount'];

$countFiltered = mysqli_query($con, "select count(*) as allcount from painting_re WHERE 1 " . $searchQuery);
if (!$countFiltered) { echo json_encode(["draw"=>intval($draw),"iTotalRecords"=>$totalRecords,"iTotalDisplayRecords"=>0,"aaData"=>[]]); die; }
$totalRecordwithFilter = mysqli_fetch_assoc($countFiltered)['allcount'];

$empQuery = "select * from painting_re WHERE 1 " . $searchQuery . " order by " . $columnName . " " . $columnSortOrder . " limit " . $row . "," . $rowperpage;
$empRecords = mysqli_query($con, $empQuery);
$data = array();
$no = $_POST['start'] + 1;

while ($row = mysqli_fetch_assoc($empRecords)) {
    $data[] = array(
        "NO" => $no++,
        "WM_CODE" => isset($row['WM_CODE']) ? $row['WM_CODE'] : '',
        "MCH" => isset($row['MCH']) ? $row['MCH'] : '',
        "IP_CODE" => isset($row['MAT_IP_CODE']) ? $row['MAT_IP_CODE'] : (isset($row['IP_CODE']) ? $row['IP_CODE'] : ''),
        "MAT_DESC" => isset($row['MAT_DESC']) ? $row['MAT_DESC'] : '',
        "AMOUNT" => isset($row['Amount']) ? $row['Amount'] : (isset($row['AMOUNT']) ? $row['AMOUNT'] : ''),
        "SLOT" => isset($row['Park']) ? $row['Park'] : (isset($row['SLOT']) ? $row['SLOT'] : ''),
        "PRINT_OUT" => isset($row['On_Insert']) ? $row['On_Insert'] : (isset($row['PRINT_OUT']) ? $row['PRINT_OUT'] : ''),
        "CURE_TIME" => isset($row['CURE_TIME']) ? $row['CURE_TIME'] : (isset($row['CURING_TIME']) ? $row['CURING_TIME'] : ''),
        "COUNT_PRINTED" => isset($row['Count_Printed']) ? $row['Count_Printed'] : (isset($row['COUNT_PRINTED']) ? $row['COUNT_PRINTED'] : ''),
        "USERNAME" => isset($row['WM_NAME_WM_SURNAME']) ? $row['WM_NAME_WM_SURNAME'] : (isset($row['USERNAME']) ? $row['USERNAME'] : ''),
        "GROUP_PAINT" => isset($row['WM_GROUP']) ? $row['WM_GROUP'] : (isset($row['GROUP_PAINT']) ? $row['GROUP_PAINT'] : ''),
        "SHIFT" => isset($row['WM_SHIFT']) ? $row['WM_SHIFT'] : (isset($row['SHIFT']) ? $row['SHIFT'] : ''),
        "RE" => isset($row['Re']) ? $row['Re'] : (isset($row['RE']) ? $row['RE'] : ''),
    );
}

echo json_encode(array(
    "draw" => intval($draw),
    "iTotalRecords" => $totalRecords,
    "iTotalDisplayRecords" => $totalRecordwithFilter,
    "aaData" => $data
));
die;
