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
  'MM_CODE'     => $_GET['MM_CODE']     ?? null,
  'cured_stts'  => $_GET['cured_stts']  ?? null,
  'Park'        => $_GET['Park']        ?? null,
];
$dateFrom = $_GET['date_from'] ?? null;
$dateTo   = $_GET['date_to']   ?? null;
$shiftAdj = $_GET['shift_adj'] ?? '';

$params     = [];
$whereParts = [];

// discover columns for build_where
$allCols = [];
try { $allCols = discover_columns($pdo, $sourceSql); } catch (\Throwable $e) {}

// normal filters
$wFilters = build_where($filters, $allCols, $dateCols, $params);
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

// fetch rows
$sql = 'SELECT * FROM (' . $sourceSql . ') src ' . $whereSql . ' ORDER BY 1';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// custom order + aliases
$COLUMNS = [
  'id',
  'MM_CODE',
  'WM_NAME_WM_SURNAME',
  'MAT_IP_CODE',
  'MAT_DESC',
  'id_paint',
  'Park',
  'tag_stock',
  'adj_stock',
  'dateCURE',
  'cured_stts',
  'dateAdj',
];
$ALIASES = [
  'id'                 => 'ID',
  'MM_CODE'            => 'Worker Code',
  'WM_NAME_WM_SURNAME' => 'Worker',
  'MAT_IP_CODE'        => 'Material IP',
  'MAT_DESC'           => 'Material Description',
  'id_paint'           => 'Paint ID',
  'Park'               => 'Park',
  'tag_stock'          => 'Tag Stock',
  'adj_stock'          => 'Adj Stock',
  'dateCURE'           => 'Date CURE',
  'cured_stts'         => 'Status',
  'dateAdj'            => 'Date Adj',
];

$now = date('Y-m-d H:i:s');
$shiftText = $shiftAdj === '1' ? 'Shift 1 (01:00–07:00)'
          : ($shiftAdj === '2' ? 'Shift 2 (07:00–16:00)'
          : ($shiftAdj === '3' ? 'Shift 3 (16:00–23:59)' : 'All shifts'));
$rangeText = sprintf('%s → %s', $from, $to);

$customHeader = [
  ['PT EVOLUZIONE TYRES'],
  ['Cure Stock Report (parking_bf_curr_stock)'],
  ['Date range:', $rangeText, 'Shift:', $shiftText],
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
