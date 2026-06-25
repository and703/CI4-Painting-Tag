# CI4-Painting-Tag

CodeIgniter 4 project — industrial painting-tag CRUD with parking, curing, and reprint workflows.

## Quick start

```bash
composer install
cp env .env              # edit to set CI_ENVIRONMENT, DB creds
php spark serve          # dev server at localhost:8080
```

## Commands

| Action | Command |
|--------|---------|
| Run tests | `composer test` (alias for `phpunit`) |
| Dev server | `php spark serve` |
| CLI tools | `php spark` |

## Architecture

- **Auto-routing** is ON (`$routes->setAutoRoute(true)`). URL `/foo/bar` maps to `Foo::bar()`. Explicit routes in `app/Config/Routes.php` take precedence.
- **Default controller:** `Worker::index` (`/` and `/worker`)
- **Controller that matters most:** `Worker` — long `save()` method dispatches by machine prefix (`A*`, `M*`, `B*`) to different parking models. Also has shift-gated curing logic.
- **Database:** Two connections — `default` (local MySQL, database `pcs`) and `pcs` group (production SQL Server at 172.21.202.240 for worker/material lookup).
- **Models:** 23 models. Most extend `CodeIgniter\Model`. Some (report models, `CQModel`, `M_Chart`) use manual `Config\Database::connect()`.
- **Views:** 56 PHP view files in `app/Views/`. Key groups: `C_U/` (curing UI), `komik/` (painting CRUD), `layouts/`.

## Key domain concepts

- **Painting** (`painting` table) — a paint job with machine, material, amount, cure time, parking slot
- **Parking** — slots P001–P030; separate tables for A (`parking`), M (`parking_m`), B (`parking_b`) machines
- **Curing** — workflow through `parking_bf_curr` → `parking_bf_curr_stock` → `bf_cure`
- **FIFO** — `fifo_park*` tables for aging material tracking per machine type
- **Shift rules** (in `Worker::get_cure_mch`): shift 1 = 06:00–07:59, shift 2 = 14:00–15:59, shift 3 = 22:00–23:59

## Testing

- Only CI4 scaffold tests exist (no project-specific tests).
- Test DB is SQLite in-memory (`$DBGroup = 'tests'`, auto-set when `ENVIRONMENT === 'testing'`).
- Test trait: `DatabaseTestTrait` for migrations + seeds + transaction rollback.
- Add new tests under `tests/` following CI4 conventions (`CIUnitTestCase`, `final class`).

## Gotchas

- `.env` is gitignored. Copy `env` to `.env` for local config.
- `BaseController::initController` calls `session()` — all views have session access.
- The `pcs` SQL Server connection requires the `sqlsrv` PHP extension.
- Git branches: `master`, `Cure`, `refactoring` (base = Cure).
