<?php
declare(strict_types=1);
date_default_timezone_set('Asia/Jakarta');

header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/util.php';

$config     = require __DIR__ . '/../config.php';
$pdo        = db();
$sourceSql  = $config['source_sql'];
$allColumns = discover_columns($pdo, $sourceSql);
$searchable = $config['searchable_columns'] ?: $allColumns;
$dateCols   = $config['date_columns'] ?? [];

/* ---------- DataTables params ---------- */
$draw   = isset($_POST['draw'])   ? (int)$_POST['draw']   : 0;
$start  = isset($_POST['start'])  ? max(0,(int)$_POST['start'])  : 0;
$length = isset($_POST['length']) ? max(10,(int)$_POST['length']) : 10;
$order  = $_POST['order'][0] ?? ['column'=>0,'dir'=>'asc'];
$colsIn = $_POST['columns'] ?? [];
$searchVal = $_POST['search']['value'] ?? '';

/* ---------- UI filters ---------- */
$filters = [
  'MAT_IP_CODE' => $_POST['MAT_IP_CODE'] ?? $_GET['MAT_IP_CODE'] ?? null,
  'MM_CODE'     => $_POST['MM_CODE']     ?? $_GET['MM_CODE']     ?? null,
  'cured_stts'  => $_POST['cured_stts']  ?? $_GET['cured_stts']  ?? null,
  'Park'        => $_POST['Park']        ?? $_GET['Park']        ?? null,
];

$dateFrom = $_POST['date_from'] ?? $_GET['date_from'] ?? null;
$dateTo   = $_POST['date_to']   ?? $_GET['date_to']   ?? null;
$shiftAdj = $_POST['shift_adj'] ?? $_GET['shift_adj'] ?? '';

/* ---------- ORDER BY (defensive) ---------- */
$colIndex = (int)($order['column'] ?? 0);
$dir      = (strtolower($order['dir'] ?? 'asc') === 'desc') ? 'DESC' : 'ASC';
$colName  = $colsIn[$colIndex]['data'] ?? ($allColumns[$colIndex] ?? ($allColumns[0] ?? 'id'));
if (!in_array($colName, $allColumns, true)) {
  $colName = $allColumns[0] ?? 'id';
}
$colNameQuoted = '`' . str_replace('`','``',$colName) . '`';

/* ---------- WHERE building (ONLY named params) ---------- */
$params     = [];   // associative ONLY
$whereParts = [];

// 1) normal column filters (LIKE/IN/date BETWEEN via util.php)
$w = build_where($filters, $allColumns, $dateCols, $params);
if ($w) $whereParts[] = preg_replace('/^WHERE\s+/i', '', $w);

// 2) dateAdj (varchar 'dd/mm/YYYY HH.mm') -> default to today range
$dtExpr   = "STR_TO_DATE(`dateAdj`, '%d/%m/%Y %H.%i')";
$dayExpr  = "DATE($dtExpr)";
$timeExpr = "TIME($dtExpr)";

$today = date('Y-m-d');
$from  = $dateFrom ?: $today;
$to    = $dateTo   ?: $today;

$whereParts[] = "$dayExpr BETWEEN :adj_from AND :adj_to";
$params[':adj_from'] = $from;
$params[':adj_to']   = $to;

// 3) shift windows on time of day
if ($shiftAdj === '1') {
  $whereParts[] = "($timeExpr >= '01:00:00' AND $timeExpr < '07:00:00')";
} elseif ($shiftAdj === '2') {
  $whereParts[] = "($timeExpr >= '07:00:00' AND $timeExpr < '16:00:00')";
} elseif ($shiftAdj === '3') {
  $whereParts[] = "($timeExpr >= '16:00:00' AND $timeExpr <= '23:59:59')";
}

// 4) global search (named params :g0, :g1, ...)
$g = build_global_search($searchVal, $searchable, $params);
if ($g) $whereParts[] = $g;

$whereSql = $whereParts ? ('WHERE ' . implode(' AND ', $whereParts)) : '';

/* ---------- counts ---------- */
$total = (int)$pdo->query('SELECT COUNT(*) FROM (' . $sourceSql . ') src')->fetchColumn();

$stmt = $pdo->prepare('SELECT COUNT(*) FROM (' . $sourceSql . ') src ' . $whereSql);
$stmt->execute($params);
$filtered = (int)$stmt->fetchColumn();

/* ---------- page rows ----------
   Avoid placeholders for LIMIT/OFFSET to prevent mixing positional/named.
   Safely inline validated integers. */
$limit  = (int)$length;
$offset = (int)$start;
$sqlPage = 'SELECT * FROM (' . $sourceSql . ') src ' . $whereSql .
           ' ORDER BY ' . $colNameQuoted . ' ' . $dir .
           ' LIMIT ' . $limit . ' OFFSET ' . $offset;

$stmt = $pdo->prepare($sqlPage);
// bind ONLY the named params built above
foreach ($params as $k => $v) {
  $stmt->bindValue($k, $v);
}
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ---------- output ---------- */
echo json_encode([
  'draw' => $draw,
  'recordsTotal' => $total,
  'recordsFiltered' => $filtered,
  'data' => $rows,
], JSON_UNESCAPED_UNICODE);
