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
$dateCols   = $config['date_columns'] ?? [];

// filters
$filters = [
  'MAT_IP_CODE' => $_GET['MAT_IP_CODE'] ?? $_POST['MAT_IP_CODE'] ?? null,
  'MM_CODE'     => $_GET['MM_CODE']     ?? $_POST['MM_CODE']     ?? null,
  'cured_stts'  => $_GET['cured_stts']  ?? $_POST['cured_stts']  ?? null,
  'Park'        => $_GET['Park']        ?? $_POST['Park']        ?? null,
];
$dateFrom = $_GET['date_from'] ?? $_POST['date_from'] ?? null;
$dateTo   = $_GET['date_to']   ?? $_POST['date_to']   ?? null;
$shiftAdj = $_GET['shift_adj'] ?? $_POST['shift_adj'] ?? '';

$params     = [];
$whereParts = [];

// normal filters
$wFilters = build_where($filters, $allColumns, $dateCols, $params);
if ($wFilters) $whereParts[] = preg_replace('/^WHERE\s+/i', '', $wFilters);

// dateAdj logic
$dtExpr   = "STR_TO_DATE(`dateAdj`, '%d/%m/%Y %H.%i')";
$dayExpr  = "DATE($dtExpr)";
$timeExpr = "TIME($dtExpr)";

$today = date('Y-m-d');
$from  = $dateFrom ?: $today;
$to    = $dateTo   ?: $today;
$whereParts[] = "$dayExpr BETWEEN :adj_from AND :adj_to";
$params[':adj_from'] = $from;
$params[':adj_to']   = $to;

// shift windows
if ($shiftAdj === '1') {
  $whereParts[] = "($timeExpr >= '01:00:00' AND $timeExpr < '07:00:00')";
} elseif ($shiftAdj === '2') {
  $whereParts[] = "($timeExpr >= '07:00:00' AND $timeExpr < '16:00:00')";
} elseif ($shiftAdj === '3') {
  $whereParts[] = "($timeExpr >= '16:00:00' AND $timeExpr <= '23:59:59')";
}

$whereSql = $whereParts ? ('WHERE ' . implode(' AND ', $whereParts)) : '';

// aggregate
$sql = 'SELECT 
          COUNT(*)                            AS rows_count,
          COALESCE(SUM(tag_stock), 0)         AS sum_tag_stock,
          COALESCE(SUM(adj_stock), 0)         AS sum_adj_stock
        FROM (' . $sourceSql . ') src ' . $whereSql;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$agg = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['rows_count'=>0,'sum_tag_stock'=>0,'sum_adj_stock'=>0];

echo json_encode($agg, JSON_UNESCAPED_UNICODE);
