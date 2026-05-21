<?php
declare(strict_types=1);
date_default_timezone_set('Asia/Jakarta');

require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/util.php';

$config    = require __DIR__ . '/../config.php';
$pdo       = db();
$sourceSql = $config['source_sql'];
$allCols   = discover_columns($pdo, $sourceSql);
$dateCols  = $config['date_columns'] ?? [];

// filters
$filters = [
  'MAT_IP_CODE' => $_GET['MAT_IP_CODE'] ?? null,
];

$params     = [];
$whereParts = [];

// normal filters
$wFilters = build_where($filters, $allCols, $dateCols, $params);
if ($wFilters) $whereParts[] = preg_replace('/^WHERE\s+/i', '', $wFilters);

$whereSql = $whereParts ? ('WHERE ' . implode(' AND ', $whereParts)) : '';

// fetch rows
$sql = 'SELECT * FROM (' . $sourceSql . ') src ' . $whereSql . ' ORDER BY 1';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ----- Custom order + aliases (same as XLSX) -----
$COLUMNS = [
  'MAT_IP_CODE',
  'Total_Amount',
  'Total_AdjStock',
  'Grand_Total',
];
$ALIASES = [
  'MAT_IP_CODE'        => 'Material IP',
  'Total_Amount'       => 'Stock at Parking area',
  'Total_AdjStock'     => 'Stock in front of machine',
  'Grand_Total'        => 'Grand Total',
];

// stream CSV
$fn = 'report_export_' . date('Ymd_His') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $fn);
$fp = fopen('php://output', 'w');
fwrite($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));

// header row
fputcsv($fp, array_map(fn($c) => $ALIASES[$c] ?? $c, $COLUMNS));

// data rows
foreach ($rows as $r) {
  $line = [];
  foreach ($COLUMNS as $col) {
    $line[] = $r[$col] ?? '';
  }
  fputcsv($fp, $line);
}
fclose($fp);
