# Advanced Report (Native PHP)

A drop-in, **framework-free** PHP report with:
- **Server-side** DataTables (fast on large datasets)
- **Filters**: date range, SKU, barcode, status, Park (extend easily)
- **Column discovery**: reads columns from your view/table dynamically
- **CSV Export (server-side)** matching current filters
- **Bootstrap 5** UI, responsive, column visibility, saved state

## Structure
```
public/
  index.php     # UI
api/
  data.php      # DataTables server-side endpoint
export/
  csv.php       # CSV export with same filters
includes/
  db.php        # PDO connection + column discovery
  util.php      # Query builder helpers
config.php      # DB settings + source SQL
```

## Setup
1. Copy the entire folder to your PHP server (e.g., `htdocs/report_advanced`).
2. Edit `config.php`:
   - Set DB credentials
   - Set `source_sql` to your table/view, e.g.:
     ```php
     'source_sql' => 'SELECT * FROM report_cure_view'
     ```
   - Optionally set `date_columns` and `searchable_columns`.
3. Open `public/index.php` in your browser.

## Notes
- Column headers and DataTables column config are **generated dynamically** from the first row returned.
- Exports are **server-side** so they include all filtered rows (not just the current page).
- To add more filters:
  1. Add input in `public/index.php` (and add to `currentFilters()`)
  2. Add to `$filters` mapping in `api/data.php` and `export/csv.php`

## Security & Performance
- Uses PDO prepared statements everywhere
- Validates sort columns against the discovered column list
- State saving enabled; column visibility and page length persist per user

## Troubleshooting
- If you see "No data", ensure `source_sql` points to a valid table/view and the DB user can `SELECT` it.
- For huge datasets, consider adding proper DB indexes on frequently filtered columns (e.g., `dateCURE`, `barcode`, `sku`).



---

## Tailored to `parking_bf_curr_stock`

This build is preset to use:
- Table: `parking_bf_curr_stock`
- Date columns: `dateCURE`, `dateAdj`
- Searchable columns: `WM_NAME_WM_SURNAME`, `MAT_IP_CODE`, `MAT_DESC`, `Park`, `cured_stts`, `dateCURE`, `dateAdj`, `MM_CODE`

### Recommended indexes
```sql
CREATE INDEX idx_pbfcs_dateCURE ON parking_bf_curr_stock (dateCURE);
CREATE INDEX idx_pbfcs_dateAdj   ON parking_bf_curr_stock (dateAdj);
CREATE INDEX idx_pbfcs_MM_CODE   ON parking_bf_curr_stock (MM_CODE);
CREATE INDEX idx_pbfcs_MAT_IP    ON parking_bf_curr_stock (MAT_IP_CODE);
CREATE INDEX idx_pbfcs_Park      ON parking_bf_curr_stock (Park);
CREATE INDEX idx_pbfcs_status    ON parking_bf_curr_stock (cured_stts);
```
