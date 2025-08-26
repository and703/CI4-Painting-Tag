<?php
/**
 * Basic configuration for Advanced Report (Native PHP)
 *
 * Edit DB credentials and SOURCE_SQL to match your table/view.
 */

return [
    'db' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'port' => (int)(getenv('DB_PORT') ?: 3306),
        'name' => getenv('DB_NAME') ?: 'pcs',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASS') ?: '',
        'charset' => 'utf8mb4',
    ],
    // IMPORTANT: set this to your table or view or a SELECT query.
    // Example: 'SELECT * FROM report_cure_view'
    'source_sql' => getenv('SOURCE_SQL') ?: 'SELECT * FROM vw_matip_totals',

    // Optional: limit which columns are searchable for global search (empty = all text columns)
    'searchable_columns' => [
        'MAT_IP_CODE'
    ],

    // Optional: declare which columns are date fields to enable date range filter.
    'date_columns' => [],
];
