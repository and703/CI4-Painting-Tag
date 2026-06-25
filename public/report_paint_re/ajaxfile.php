<?php
include 'config.php';

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

$table = "pcs.painting_re";

$sel = mysqli_query($con, "select count(*) as allcount from $table");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

$sel = mysqli_query($con, "select count(*) as allcount from $table WHERE 1 " . $searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

$empQuery = "select * from $table WHERE 1 " . $searchQuery . " order by " . $columnName . " " . $columnSortOrder . " limit " . $row . "," . $rowperpage;
$empRecords = mysqli_query($con, $empQuery);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {
    $data[] = array(
        "WM_CODE" => $row['WM_CODE'],
        "MCH" => $row['MCH'],
        "IP_CODE" => $row['MAT_IP_CODE'],
        "MAT_DESC" => $row['MAT_DESC'],
        "AMOUNT" => $row['Amount'],
        "SLOT" => $row['Park'],
        "PRINT_OUT" => $row['On_Insert'],
        "CURE_TIME" => $row['CURE_TIME'],
        "COUNT_PRINTED" => $row['Count_Printed'],
        "USERNAME" => $row['WM_NAME_WM_SURNAME'],
        "GROUP_PAINT" => $row['WM_GROUP'],
        "SHIFT" => $row['WM_SHIFT'],
        "RE" => $row['Re'],
    );
}

$response = array(
    "draw" => intval($draw),
    "iTotalRecords" => $totalRecords,
    "iTotalDisplayRecords" => $totalRecordwithFilter,
    "aaData" => $data
);

echo json_encode($response);
die;
