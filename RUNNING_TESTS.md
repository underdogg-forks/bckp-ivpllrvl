# Quick Test Reference Guide

## Running Tests

### All Service Tests
```bash
# Run all service unit tests
php artisan test tests/Unit/Services

# With verbose output
php artisan test tests/Unit/Services --verbose

# With parallel execution (faster)
php artisan test tests/Unit/Services --parallel

# Stop on first failure
php artisan test tests/Unit/Services --stop-on-failure
```

### Individual Service Tests
```bash
# Units service
php artisan test tests/Unit/Services/Units/UnitsServiceTest.php

# Tasks service
php artisan test tests/Unit/Services/Tasks/TasksServiceTest.php

# Clients service
php artisan test tests/Unit/Services/Clients/ClientsServiceTest.php

# Projects service
php artisan test tests/Unit/Services/Projects/ProjectsServiceTest.php

# Products service
php artisan test tests/Unit/Services/Products/ProductsServiceTest.php

# Invoices service
php artisan test tests/Unit/Services/Invoices/InvoicesServiceTest.php

# Quotes service
php artisan test tests/Unit/Services/Quotes/QuotesServiceTest.php

# Payments service
php artisan test tests/Unit/Services/Payments/PaymentsServiceTest.php
```

### Edge Cases & Integration Tests
```bash
# Edge cases
php artisan test tests/Unit/Services/EdgeCasesTest.php

# Integration tests
php artisan test tests/Unit/Services/Integration/TaskInvoiceIntegrationTest.php
```

### Specific Test Methods
```bash
# Run a specific test method
php artisan test --filter=it_returns_all_units

# Run tests matching a pattern
php artisan test --filter=unit_service
```

## Test Coverage

```bash
# Generate coverage report (requires Xdebug or PCOV)
php artisan test tests/Unit/Services --coverage

# HTML coverage report
php artisan test tests/Unit/Services --coverage-html coverage

# Minimum coverage threshold
php artisan test tests/Unit/Services --min=80
```

## Debugging Tests

```bash
# Show detailed test output
php artisan test tests/Unit/Services -vvv

# Stop on first failure
php artisan test tests/Unit/Services --stop-on-failure

# Show test execution times
php artisan test tests/Unit/Services --profile
```

## Using PHPUnit Directly

```bash
# Run with PHPUnit
./vendor/bin/phpunit tests/Unit/Services

# With colors
./vendor/bin/phpunit tests/Unit/Services --colors=always

# With testdox format (readable output)
./vendor/bin/phpunit tests/Unit/Services --testdox
```

## Watch Mode (with tools like `phpunit-watcher`)

```bash
# Install watcher
composer require --dev spatie/phpunit-watcher

# Watch for changes and auto-run tests
./vendor/bin/phpunit-watcher watch
```

## Continuous Integration

### GitHub Actions Example
```yaml
- name: Run Service Tests
  run: php artisan test tests/Unit/Services --parallel
```

### GitLab CI Example
```yaml
test:services:
  script:
    - php artisan test tests/Unit/Services --parallel
```

## Common Issues

### Database Connection
If you see database errors:
```bash
# Ensure .env.testing exists with in-memory database
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

### Memory Issues
For large test suites:
```bash
# Increase memory limit
php -d memory_limit=512M artisan test tests/Unit/Services
```

## Test Organization