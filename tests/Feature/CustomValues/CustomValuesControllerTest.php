<?php

namespace Tests\Feature\CustomValues;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use src\Controllers\CustomValuesController;
use src\Models\CustomValue;
use Tests\TestCase;

#[CoversClass(CustomValuesController::class)]
class CustomValuesControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_custom_values()
    {
        // Arrange: create custom values
        $customValue = CustomValue::factory()->create(['name' => 'Test Value']);

        // Act: call the index route
        $response = $this->get(route('custom_values.index'));

        // Assert: custom value is visible in the response
        $response->assertStatus(200);
        $response->assertSee('Test Value');
    }

    #[Test]
    public function it_displays_and_saves_custom_field()
    {
        // Arrange: create a custom field
        $customField     = \Modules\CustomFields\Models\CustomField::factory()->create(['name' => 'Test Field']);
        $customValueData = [
            'value' => 'New Value',
            // add other required fields
        ];

        // Act: post to the field route to save a value
        $response = $this->post(route('custom_values.field', ['id' => $customField->id]), $customValueData);

        // Assert: custom value is saved
        $this->assertDatabaseHas('custom_values', ['value' => 'New Value']);
        $response->assertRedirect(route('custom_values'));
    }
}
