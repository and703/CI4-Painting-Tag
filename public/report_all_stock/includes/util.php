<?php
declare(strict_types=1);

/**
 * Build a WHERE clause (prefixed with "WHERE ") using ONLY named parameters.
 *
 * $filters rules:
 *   - ['col' => 'text']            => `col` LIKE :col   (wraps with %...%)
 *   - ['col' => ['a','b']]         => `col` IN (:col_0, :col_1, ...)
 *   - ['dateCol' => ['from'=>..,'to'=>..]] where dateCol is in $dateCols
 *                                   => `dateCol` BETWEEN :dateCol_from AND :dateCol_to
 *
 * @param array        $filters     key => string|array|null
 * @param array        $allowedCols list of valid column names
 * @param array        $dateCols    columns that support date range ['from','to']
 * @param array<string,mixed> &$params Accumulator for named bind params (WILL be mutated)
 * @return string       "WHERE ..." or "" if nothing
 */
function build_where(array $filters, array $allowedCols, array $dateCols, array &$params): string {
    $parts = [];

    foreach ($filters as $col => $val) {
        if ($val === null || $val === '') continue;
        if (!in_array($col, $allowedCols, true)) continue;

        $colQuoted = '`' . str_replace('`', '``', $col) . '`'; // backtick-safe

        // Array values -> IN list with unique param names
        if (is_array($val)) {
            $inParams = [];
            foreach ($val as $i => $v) {
                $p = sprintf(':%s_%d', $col, $i);
                $inParams[] = $p;
                $params[$p] = $v;
            }
            if (!empty($inParams)) {
                $parts[] = $colQuoted . ' IN (' . implode(',', $inParams) . ')';
            }
            continue;
        }

        // String -> LIKE (wrap with %...%)
        if (is_string($val)) {
            $p = ':' . $col;
            // If param name already taken, make it unique
            $idx = 1;
            $pBase = $p;
            while (array_key_exists($p, $params)) {
                $p = $pBase . '_' . $idx++;
            }
            $parts[] = "$colQuoted LIKE $p";
            $params[$p] = '' . $val . '%';
            continue;
        }
    }

    return $parts ? ('WHERE ' . implode(' AND ', $parts)) : '';
}

/**
 * Global search across selected columns, using ONLY named parameters (:g0, :g1, ...)
 *
 * @param ?string $search
 * @param array   $searchable list of column names
 * @param array<string,mixed> &$params
 * @return string SQL like "(col1 LIKE :g0 OR col2 LIKE :g1 ...)" or ""
 */
function build_global_search(?string $search, array $searchable, array &$params): string {
    if ($search === null || $search === '') return '';

    $likes = [];
    $i = 0;
    foreach ($searchable as $col) {
        if ($col === '' || $col === null) continue;
        $colQuoted = '`' . str_replace('`', '``', $col) . '`';
        $p = ':g' . $i++;
        // Make sure we don't collide with existing keys
        while (array_key_exists($p, $params)) {
            $p = ':g' . $i++;
        }
        $likes[] = "$colQuoted LIKE $p";
        $params[$p] = '%' . $search . '%';
    }

    return $likes ? '(' . implode(' OR ', $likes) . ')' : '';
}
