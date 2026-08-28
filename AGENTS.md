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
├── Cache/        - Cache helpers
├── Config/       - DB-backed configuration
├── Console/      - Artisan commands
├── Database/     - Database diagnostics
├── Exceptions/   - Custom exceptions
├── Features/     - Feature flags
├── Files/        - File attachment helpers
├── Health/       - Health checks
├── Http/         - HTTP client and middleware
├── Logging/      - Logging helpers
├── Mail/         - Mail helpers
├── Metrics/      - Metrics
├── Notifications - Notification helpers
├── Query/        - Query builder helpers
├── Resources/    - CRUD resource system
├── Security/     - Security utilities
├── Support/      - Core Fuse class and Facade
├── Testing/      - Testing helpers
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
