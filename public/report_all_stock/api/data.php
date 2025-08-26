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
];

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
