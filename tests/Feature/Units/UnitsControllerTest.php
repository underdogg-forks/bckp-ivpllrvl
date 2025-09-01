<?php

namespace Tests\Feature\Units;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Units\Controllers\UnitsController;
use Tests\TestCase;

#[CoversClass(UnitsController::class)]
class UnitsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_units_list()
    {
        // Arrange: create units
        $unit = \Modules\Units\Models\Unit::factory()->create(['unit_name' => 'Test Unit']);

        // Act: call the index route
        $response = $this->get(route('units.index'));

        // Assert: unit is visible in the response
        $response->assertStatus(200);
        $response->assertSee('Test Unit');
    }

    #[Test]
    public function it_handles_unit_creation_and_editing()
    {
        // Arrange: unit data
        $data = [
            'unit_name'      => 'New Unit',
            'unit_name_plrl' => 'New Units',
            'is_update'      => 0,
        ];

        // Act: submit the form to create a unit
        $response = $this->post(route('units.form'), $data);

        // Assert: unit is created in the database
        $this->assertDatabaseHas('ip_units', ['unit_name' => 'New Unit', 'unit_name_plrl' => 'New Units']);
        $response->assertRedirect(route('units'));

        // Act: edit the unit
        $unit     = \Modules\Units\Models\Unit::query()->first();
        $editData = [
            'unit_name'      => 'Updated Unit',
            'unit_name_plrl' => 'Updated Units',
            'is_update'      => 1,
        ];
        $response = $this->post(route('units.form', $unit->id), $editData);

        // Assert: unit is updated
        $this->assertDatabaseHas('ip_units', ['unit_name' => 'Updated Unit', 'unit_name_plrl' => 'Updated Units']);
        $response->assertRedirect(route('units'));
    }
}
