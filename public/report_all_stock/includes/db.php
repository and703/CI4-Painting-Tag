<?php
declare(strict_types=1);

$config = require __DIR__ . '/../config.php';

/** @return PDO */
function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    $cfg = $GLOBALS['config']['db'] ?? $config['db'];
    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $cfg['host'], $cfg['port'], $cfg['name'], $cfg['charset']
    );
    $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

/**
 * Get safe list of column names from the SOURCE_SQL by running a LIMIT 0 query.
 * This lets us validate order/search parameters at runtime.
 *
 * @return string[] column names
 */
function discover_columns(PDO $pdo, string $sourceSql): array {
    $stmt = $pdo->query('SELECT * FROM (' . $sourceSql . ') as src LIMIT 0');
    $cols = [];
    for ($i = 0; $i < $stmt->columnCount(); $i++) {
        $meta = $stmt->getColumnMeta($i);
        if (!empty($meta['name'])) $cols[] = $meta['name'];
    }
    return $cols;
}
