# AGENTS.md

## Fuse Package Development

This is the `synetro/fuse` Laravel package.

### Running Tests

```bash
composer install
vendor/bin/phpunit
```

### Linting

```bash
vendor/bin/pint
```

### Generating Code

```bash
php artisan fuse:make Product --full
```

### Package Structure

```
src/
├── Actions/      - Action abstraction and manager
├── Api/          - API response formatting
├── Auth/         - Authorization helpers
├── Bulk/         - Bulk Eloquent operations (update, delete, restore)
├── Cache/        - Cache helpers
├── Config/       - DB-backed configuration
├── Console/      - Artisan commands (install, doctor, auth, security, cleanup, ...)
├── Database/     - Database diagnostics
├── Discovery/    - Auto-discovery of Actions, Policies, Resources, etc.
├── Exceptions/   - Custom exceptions
├── Features/     - Feature flags
├── Files/        - File attachment helpers
├── Health/       - Health checks
├── Http/         - HTTP client and middleware
├── Idempotency/  - Idempotent execution wrapper
├── ImportExport/ - CSV/JSON import and export with chunking
├── Locks/        - Distributed lock wrapper using atomic locks
├── Logging/      - Logging helpers
├── Mail/         - Mail helpers
├── Metrics/      - Metrics
├── Notifications - Notification helpers
├── Profiling/    - Query profiling (N+1, duplicates, memory)
├── Query/        - Query builder helpers
├── RateLimit/    - Rate limiting wrapper using Laravel RateLimiter
├── Resources/    - CRUD resource system
├── Security/     - Security utilities
├── Support/      - Core Fuse class and Facade
├── Testing/      - Testing helpers
├── Usage/        - Usage counters and quota management
├── Validation/   - Concise validation wrapper around Laravel Validator
├── Webhooks/     - Webhook system
└── Workflow/     - Pipeline/workflow system

config/
database/migrations/
routes/
stubs/
tests/
```

### Key Files

- `src/FuseServiceProvider.php` - Main service provider
- `src/Support/Fuse.php` - Main API entry point
- `src/Support/Facades/Fuse.php` - Facade
- `config/fuse.php` - Configuration
- `src/Support/helpers.php` - Global helper functions

### Testing Notes

- Uses Orchestra Testbench
- All tests should extend `Synetro\Fuse\Testing\TestCase`
- Database is sqlite in-memory by default
