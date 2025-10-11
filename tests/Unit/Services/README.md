# Service Unit Tests

This directory contains comprehensive unit tests for service layer modifications made in this branch.

## Test Coverage

The following services have been tested with focus on:
- New methods added in this branch
- Refactored methods migrating from legacy database calls to Eloquent
- Edge cases and error handling
- Input validation and boundary conditions

### Services Tested

#### 1. UnitsService
- `getAll()` - Retrieve all units
- `exists()` - Check unit existence by name
- `getName()` - Get singular/plural forms based on quantity
- `save()` - Create and update units
- `delete()` - Delete units
- Validation rules
- Edge cases: null/zero quantities, non-existent IDs

#### 2. TasksService
- `byTask()` - Filter tasks by name/description
- `getInvoiceForTask()` - Retrieve associated invoice
- `getTasksToInvoice()` - Get billable tasks
- `updateOnInvoiceDelete()` - Handle invoice deletion
- `updateOnProjectDelete()` - Handle project deletion
- Edge cases: null IDs, empty results, data integrity

#### 3. ClientsService
- `getActive()` - Retrieve active clients
- `isActive()` - Query builder for active clients
- `isInactive()` - Query builder for inactive clients
- `getNotAssignedToUser()` - Get unassigned clients
- Edge cases: no active clients, no assignments

#### 4. ProjectsService
- `getTasks()` - Retrieve project tasks
- Edge cases: null/zero/false project IDs, empty projects

#### 5. ProductsService
- `getByIds()` - Bulk retrieve products
- Edge cases: empty arrays, non-existent IDs, duplicates

#### 6. InvoicesService
- `getPayments()` - Attach payments to invoice
- `markViewed()` - Update invoice status when viewed
- `byClient()` - Filter invoices by client
- Edge cases: no payments, incorrect statuses

#### 7. QuotesService
- `markViewed()` - Update quote status when viewed
- `byClient()` - Filter quotes by client
- `dbArray()` - Database array structure

#### 8. PaymentsService
- `whereInvoiceId()` - Filter payments by invoice
- `dbArray()` - Database array structure

#### 9. CustomFieldsService
- `byTable()` - Filter custom fields by table
- Validation rules

#### 10. FamiliesService
- Default select and validation rules

#### 11. TaxRatesService
- Default select and validation rules
- Edge cases for tax rate storage

#### 12. InvoiceGroupsService
- Default select and ordering
- Validation rules

#### 13. PaymentMethodsService
- Default select and ordering by name
- Validation rules

#### 14. UsersService
- Default select and validation rules

## Test Patterns

### AAA Pattern (Arrange-Act-Assert)
All tests follow the AAA pattern for clarity:
```php
// Arrange: Set up test data and conditions
$unit = Unit::create(['unit_name' => 'Hour', 'unit_name_plrl' => 'Hours']);

// Act: Execute the method under test
$result = $this->service->getName($unit->unit_id, 1);

// Assert: Verify the outcome
$this->assertEquals('Hour', $result);
```

### Database Transactions
Tests use `RefreshDatabase` trait to ensure:
- Clean state before each test
- Automatic rollback after each test
- No test pollution

### Descriptive Test Names
Test method names clearly describe what is being tested:
- `it_returns_singular_name_for_quantity_of_one()`
- `it_throws_exception_when_updating_non_existent_unit()`
- `it_clears_project_association_when_project_is_deleted()`

## Running Tests

### Run all service tests
```bash
php artisan test tests/Unit/Services
```

### Run specific service test
```bash
php artisan test tests/Unit/Services/Units/UnitsServiceTest.php
```

### Run with coverage
```bash
php artisan test --coverage
```

## Test Attributes

Tests use PHP 8 attributes for metadata:
- `#[Test]` - Marks a method as a test
- Future: `#[Group]`, `#[DataProvider]` for advanced scenarios

## Best Practices Applied

1. **Isolated Tests**: Each test is independent and can run in any order
2. **Clear Assertions**: Single, focused assertion per test where possible
3. **Edge Cases**: Comprehensive coverage of boundary conditions
4. **Error Handling**: Tests for exception scenarios
5. **Real Models**: Uses actual Eloquent models, not mocks (for unit tests of services)
6. **Database State**: Tests verify both return values and database state changes

## Future Enhancements

- Add data providers for parameterized tests
- Integration tests for complex multi-service workflows
- Performance benchmarks for critical queries
- Mock external dependencies where applicable