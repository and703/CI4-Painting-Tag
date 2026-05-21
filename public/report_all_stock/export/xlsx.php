<?php
declare(strict_types=1);
date_default_timezone_set('Asia/Jakarta');

require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/util.php';
require __DIR__ . '/../includes/SimpleXLSXGen.php';

use Shuchkin\SimpleXLSXGen;

$config    = require __DIR__ . '/../config.php';
$pdo       = db();
$sourceSql = $config['source_sql'];
$dateCols  = $config['date_columns'] ?? [];

// filters
$filters = [
  'MAT_IP_CODE' => $_GET['MAT_IP_CODE'] ?? null,
];

$params     = [];
$whereParts = [];

// discover columns for build_where
$allCols = [];
try { $allCols = discover_columns($pdo, $sourceSql); } catch (\Throwable $e) {}

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

$now = date('Y-m-d H:i:s');

$customHeader = [
  ['PT EVOLUZIONE TYRES'],
  ['Cure Stock Report (parking_bf_curr_stock)'],
  ['Generated at:', $now],
  []
];

$sheet = $customHeader;
$sheet[] = array_map(fn($k) => $ALIASES[$k] ?? $k, $COLUMNS);

foreach ($rows as $r) {
  $line = [];
  foreach ($COLUMNS as $col) $line[] = $r[$col] ?? '';
  $sheet[] = $line;
}

$xlsx = SimpleXLSXGen::fromArray($sheet, 'Report');
$filename = 'report_cure_' . date('Ymd_His') . '.xlsx';
$xlsx->downloadAs($filename);
