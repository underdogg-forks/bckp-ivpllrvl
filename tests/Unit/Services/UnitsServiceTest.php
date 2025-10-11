<?php

namespace Tests\Unit\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Units\Models\Unit;
use Modules\Units\Services\UnitsService;
use Tests\TestCase;

class UnitsServiceTest extends TestCase
{
    use RefreshDatabase;

    private UnitsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UnitsService();
    }

    public function test_getName_returns_singular_for_quantity_of_one(): void
    {
        // Arrange: Create a unit
        $unit = Unit::factory()->create([
            'unit_name' => 'Hour',
            'unit_name_plrl' => 'Hours',
        ]);

        // Act: Get name for quantity 1
        $result = $this->service->getName($unit->unit_id, 1);

        // Assert: Should return singular name
        $this->assertEquals('Hour', $result);
    }

    public function test_getName_returns_singular_for_quantity_of_zero(): void
    {
        // Arrange: Create a unit
        $unit = Unit::factory()->create([
            'unit_name' => 'Item',
            'unit_name_plrl' => 'Items',
        ]);

        // Act: Get name for quantity 0
        $result = $this->service->getName($unit->unit_id, 0);

        // Assert: Should return singular name
        $this->assertEquals('Item', $result);
    }

    public function test_getName_returns_singular_for_quantity_of_negative_one(): void
    {
        // Arrange: Create a unit
        $unit = Unit::factory()->create([
            'unit_name' => 'Piece',
            'unit_name_plrl' => 'Pieces',
        ]);

        // Act: Get name for quantity -1
        $result = $this->service->getName($unit->unit_id, -1);

        // Assert: Should return singular name
        $this->assertEquals('Piece', $result);
    }

    public function test_getName_returns_plural_for_quantity_greater_than_one(): void
    {
        // Arrange: Create a unit
        $unit = Unit::factory()->create([
            'unit_name' => 'Box',
            'unit_name_plrl' => 'Boxes',
        ]);

        // Act: Get name for quantity 5
        $result = $this->service->getName($unit->unit_id, 5);

        // Assert: Should return plural name
        $this->assertEquals('Boxes', $result);
    }

    public function test_getName_returns_plural_for_quantity_less_than_negative_one(): void
    {
        // Arrange: Create a unit
        $unit = Unit::factory()->create([
            'unit_name' => 'Kilogram',
            'unit_name_plrl' => 'Kilograms',
        ]);

        // Act: Get name for quantity -5
        $result = $this->service->getName($unit->unit_id, -5);

        // Assert: Should return plural name
        $this->assertEquals('Kilograms', $result);
    }

    public function test_getName_returns_null_for_nonexistent_unit(): void
    {
        // Act: Get name for non-existent unit
        $result = $this->service->getName(99999, 1);

        // Assert: Should return null
        $this->assertNull($result);
    }

    public function test_getAll_returns_all_units(): void
    {
        // Arrange: Create multiple units
        Unit::factory()->create(['unit_name' => 'Hour', 'unit_name_plrl' => 'Hours']);
        Unit::factory()->create(['unit_name' => 'Day', 'unit_name_plrl' => 'Days']);
        Unit::factory()->create(['unit_name' => 'Piece', 'unit_name_plrl' => 'Pieces']);

        // Act: Get all units
        $result = $this->service->getAll();

        // Assert: Should return all units
        $this->assertCount(3, $result);
        $this->assertContainsOnlyInstancesOf(Unit::class, $result);
    }

    public function test_getAll_returns_empty_collection_when_no_units(): void
    {
        // Act: Get all units when database is empty
        $result = $this->service->getAll();

        // Assert: Should return empty collection
        $this->assertCount(0, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

    public function test_exists_returns_true_for_existing_unit_name(): void
    {
        // Arrange: Create a unit
        Unit::factory()->create(['unit_name' => 'Meter', 'unit_name_plrl' => 'Meters']);

        // Act & Assert
        $this->assertTrue($this->service->exists('Meter'));
    }

    public function test_exists_returns_false_for_nonexistent_unit_name(): void
    {
        // Act & Assert
        $this->assertFalse($this->service->exists('NonExistent'));
    }

    public function test_exists_is_case_sensitive(): void
    {
        // Arrange: Create a unit
        Unit::factory()->create(['unit_name' => 'Meter', 'unit_name_plrl' => 'Meters']);

        // Act & Assert: Different case should not match (depends on database collation)
        $this->assertFalse($this->service->exists('meter'));
        $this->assertFalse($this->service->exists('METER'));
    }

    public function test_save_creates_new_unit_when_id_is_null(): void
    {
        // Arrange: Prepare unit data
        $data = [
            'unit_name' => 'Liter',
            'unit_name_plrl' => 'Liters',
        ];

        // Act: Save without ID
        $unit = $this->service->save($data, null);

        // Assert: Unit should be created
        $this->assertInstanceOf(Unit::class, $unit);
        $this->assertDatabaseHas('ip_units', [
            'unit_name' => 'Liter',
            'unit_name_plrl' => 'Liters',
        ]);
    }

    public function test_save_creates_new_unit_when_id_is_empty(): void
    {
        // Arrange: Prepare unit data
        $data = [
            'unit_name' => 'Gallon',
            'unit_name_plrl' => 'Gallons',
        ];

        // Act: Save without ID (empty)
        $unit = $this->service->save($data);

        // Assert: Unit should be created
        $this->assertInstanceOf(Unit::class, $unit);
        $this->assertDatabaseHas('ip_units', [
            'unit_name' => 'Gallon',
            'unit_name_plrl' => 'Gallons',
        ]);
    }

    public function test_save_updates_existing_unit_when_id_provided(): void
    {
        // Arrange: Create an existing unit
        $unit = Unit::factory()->create([
            'unit_name' => 'Hour',
            'unit_name_plrl' => 'Hours',
        ]);

        // Act: Update the unit
        $updatedData = [
            'unit_name' => 'Hour (Updated)',
            'unit_name_plrl' => 'Hours (Updated)',
        ];
        $result = $this->service->save($updatedData, $unit->unit_id);

        // Assert: Unit should be updated
        $this->assertInstanceOf(Unit::class, $result);
        $this->assertEquals($unit->unit_id, $result->unit_id);
        $this->assertDatabaseHas('ip_units', [
            'unit_id' => $unit->unit_id,
            'unit_name' => 'Hour (Updated)',
            'unit_name_plrl' => 'Hours (Updated)',
        ]);
    }

    public function test_save_throws_exception_when_updating_nonexistent_unit(): void
    {
        // Arrange: Non-existent ID
        $data = [
            'unit_name' => 'Test',
            'unit_name_plrl' => 'Tests',
        ];

        // Act & Assert: Should throw RuntimeException
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unit not found');
        $this->service->save($data, 99999);
    }

    public function test_delete_removes_existing_unit(): void
    {
        // Arrange: Create a unit
        $unit = Unit::factory()->create([
            'unit_name' => 'ToDelete',
            'unit_name_plrl' => 'ToDeletes',
        ]);

        // Act: Delete the unit
        $result = $this->service->delete($unit->unit_id);

        // Assert: Unit should be deleted
        $this->assertTrue($result);
        $this->assertDatabaseMissing('ip_units', [
            'unit_id' => $unit->unit_id,
        ]);
    }

    public function test_delete_returns_false_for_nonexistent_unit(): void
    {
        // Act: Delete non-existent unit
        $result = $this->service->delete(99999);

        // Assert: Should return false
        $this->assertFalse($result);
    }

    public function test_validationRules_returns_correct_structure(): void
    {
        // Act: Get validation rules
        $rules = $this->service->validationRules();

        // Assert: Should have correct structure
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('unit_name', $rules);
        $this->assertArrayHasKey('unit_name_plrl', $rules);
        
        $this->assertEquals('unit_name', $rules['unit_name']['field']);
        $this->assertEquals('required', $rules['unit_name']['rules']);
        
        $this->assertEquals('unit_name_plrl', $rules['unit_name_plrl']['field']);
        $this->assertEquals('required', $rules['unit_name_plrl']['rules']);
    }

    public function test_defaultSelect_returns_query_builder(): void
    {
        // Act: Get default select
        $query = $this->service->defaultSelect();

        // Assert: Should return query builder
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $query);
    }

    public function test_defaultOrderBy_returns_ordered_query(): void
    {
        // Arrange: Create units in non-alphabetical order
        Unit::factory()->create(['unit_name' => 'Zebra', 'unit_name_plrl' => 'Zebras']);
        Unit::factory()->create(['unit_name' => 'Apple', 'unit_name_plrl' => 'Apples']);
        Unit::factory()->create(['unit_name' => 'Mango', 'unit_name_plrl' => 'Mangos']);

        // Act: Get ordered units
        $units = $this->service->defaultOrderBy()->get();

        // Assert: Should be ordered by unit_name
        $this->assertEquals('Apple', $units[0]->unit_name);
        $this->assertEquals('Mango', $units[1]->unit_name);
        $this->assertEquals('Zebra', $units[2]->unit_name);
    }
}