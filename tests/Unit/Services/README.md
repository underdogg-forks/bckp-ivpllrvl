# Service Unit Tests

This directory contains comprehensive unit tests for the service layer modifications made in the current branch compared to develop.

## Test Coverage

### ClientsServiceTest.php
Tests for `Modules/Clients/Services/ClientsService.php`

**Methods Tested:**
- `isActive()` - Returns query builder for active clients (client_active = 1)
- `isInactive()` - Returns query builder for inactive clients (client_active = 0)
- `getNotAssignedToUser($user_id)` - Returns active clients not assigned to a specific user
- `getActive()` - Returns all active clients

**Test Scenarios:**
- Active/inactive client filtering
- User assignment filtering
- Empty result handling
- Multiple user scenarios
- Collection type validation

### UnitsServiceTest.php
Tests for `Modules/Units/Services/UnitsService.php`

**Methods Tested:**
- `getName($unit_id, $quantity)` - Returns singular or plural unit name based on quantity
- `getAll()` - Returns all units
- `exists($unit_name)` - Checks if a unit exists by name
- `save($data, $id)` - Creates or updates a unit
- `delete($id)` - Deletes a unit
- `validationRules()` - Returns validation rules structure
- `defaultSelect()` - Returns base query builder
- `defaultOrderBy()` - Returns ordered query builder

**Test Scenarios:**
- Singular/plural name selection (quantity: -2, -1, 0, 1, 2+)
- CRUD operations (Create, Read, Update, Delete)
- Validation for non-existent units
- Case-sensitive existence checks
- Exception handling for invalid operations
- Query ordering

### InvoicesServiceTest.php
Tests for `Modules/Invoices/Services/InvoicesService.php`

**Methods Tested:**
- `markViewed($invoice_id)` - Updates invoice status from sent to viewed
- `markSent($invoice_id)` - Updates invoice status from draft to sent
- `getPayments($invoice)` - Attaches payment records to invoice object
- `generateInvoiceNumberIfApplicable($invoice_id)` - Generates invoice number for drafts
- `updateInvoiceDueDate($invoice_id)` - Updates due date when allowed

**Test Scenarios:**
- Status transitions (draft → sent → viewed)
- Read-only flag handling based on configuration
- Payment attachment (with/without payments)
- Invoice number generation rules
- Due date updates with read-only and configuration checks
- Non-existent invoice handling
- Conditional update logic (only when changes needed)

### QuotesServiceTest.php
Tests for `Modules/Quotes/Services/QuotesService.php`

**Methods Tested:**
- `approveQuoteByKey($quote_url_key)` - Approves quote by public URL key
- `rejectQuoteByKey($quote_url_key)` - Rejects quote by public URL key
- `approveQuoteById($quote_id)` - Approves quote by ID
- `rejectQuoteById($quote_id)` - Rejects quote by ID
- `markViewed($quote_id)` - Changes status from sent to viewed
- `markSent($quote_id)` - Changes status from draft to sent
- `generateQuoteNumberIfApplicable($quote_id)` - Generates quote number for drafts

**Test Scenarios:**
- Approval/rejection workflows for different statuses
- URL key vs ID operations
- Status transition rules (draft/sent/viewed/approved/rejected)
- Quote number generation conditions
- Non-existent quote handling
- Key-specific targeting (multiple quotes)

### TasksServiceTest.php
Tests for `Modules/Tasks/Services/TasksService.php`

**Methods Tested:**
- `byTask($match)` - Filters tasks by name or description
- `getInvoiceForTask($task_id)` - Retrieves invoice associated with a task
- `getTasksToInvoice($invoice_id)` - Returns eligible tasks for invoicing
- `updateOnInvoiceDelete($invoice_id)` - Updates task status when invoice is deleted

**Test Scenarios:**
- Text search (name and description, case-insensitive)
- Invoice-task relationships
- Task eligibility for invoicing (status, project, client matching)
- Project-based task filtering
- Task ordering (by finish date and name)
- Batch updates on invoice deletion
- Null/zero ID handling

## Running the Tests

### Run all service tests:
```bash
php artisan test --testsuite=Unit tests/Unit/Services/
```

### Run a specific test file:
```bash
php artisan test tests/Unit/Services/ClientsServiceTest.php
php artisan test tests/Unit/Services/UnitsServiceTest.php
php artisan test tests/Unit/Services/InvoicesServiceTest.php
php artisan test tests/Unit/Services/QuotesServiceTest.php
php artisan test tests/Unit/Services/TasksServiceTest.php
```

### Run a specific test method:
```bash
php artisan test --filter=test_isActive_returns_active_clients_query
```

## Test Methodology

All tests follow these principles:

1. **Arrange-Act-Assert Pattern**: Clear separation of setup, execution, and verification
2. **RefreshDatabase Trait**: Each test runs in a clean database state
3. **Factory Usage**: Leveraging Laravel factories for test data creation
4. **Descriptive Names**: Test names clearly describe what is being tested
5. **Edge Cases**: Testing happy paths, error conditions, and boundary cases
6. **Isolation**: Tests are independent and don't rely on execution order

## Coverage Focus

These tests focus on:
- **Business Logic**: Verifying correct behavior of service methods
- **Data Integrity**: Ensuring database operations maintain consistency
- **Status Transitions**: Validating state machine behavior
- **Edge Cases**: Null values, empty collections, non-existent records
- **Relationships**: Testing joins and foreign key relationships
- **Configuration**: Testing behavior based on application settings

## Dependencies

Tests use these factories (must exist):
- `Client::factory()`
- `UserClient::factory()`
- `Unit::factory()`
- `Invoice::factory()`
- `Payment::factory()`
- `Quote::factory()`
- `Task::factory()`
- `Item::factory()`
- `Project::factory()`

## Notes

- Tests assume SQLite in-memory database (configured in phpunit.xml)
- Configuration mocking uses Laravel's `config()` helper
- Tests validate Eloquent model relationships and query builder operations
- Service classes migrated from legacy database operations to Eloquent ORM