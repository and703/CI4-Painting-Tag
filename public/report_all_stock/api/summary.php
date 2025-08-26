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
];

$params     = [];
$whereParts = [];

// normal filters
$wFilters = build_where($filters, $allColumns, $dateCols, $params);
if ($wFilters) $whereParts[] = preg_replace('/^WHERE\s+/i', '', $wFilters);

$whereSql = $whereParts ? ('WHERE ' . implode(' AND ', $whereParts)) : '';

// aggregate
$sql = 'SELECT 
          COUNT(*)                            AS rows_count,
          COALESCE(SUM(Total_Amount), 0)      AS sum_Total_Amount,
          COALESCE(SUM(Total_AdjStock), 0)    AS sum_Total_AdjStock
        FROM (' . $sourceSql . ') src ' . $whereSql;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$agg = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['rows_count'=>0,'sum_Total_Amount'=>0,'sum_Total_AdjStock'=>0];

echo json_encode($agg, JSON_UNESCAPED_UNICODE);
