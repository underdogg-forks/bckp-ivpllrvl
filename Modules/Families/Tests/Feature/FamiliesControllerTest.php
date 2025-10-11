<?php

namespace Modules\Families\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Families\Controllers\FamiliesController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use function Tests\Feature\Families\route;

#[CoversClass(FamiliesController::class)]
class FamiliesControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_families_list()
    {
        // Arrange: create families
        $family = \Modules\Families\Models\Family::factory()->create();

        // Act: visit families index
        $response = $this->get(route('families.index'));

        // Assert: families are displayed
        $response->assertStatus(200);
        $response->assertViewIs('families.index');
        $response->assertSee($family->family_name);
    }

    #[Test]
    public function it_displays_family_form_for_new_family()
    {
        // Act: visit new family form
        $response = $this->get(route('families.form'));

        // Assert: form is displayed
        $response->assertStatus(200);
    }

    #[Test]
    public function it_redirects_when_cancel_button_is_clicked()
    {
        // Act: submit form with cancel button
        $response = $this->post(route('families.form'), [
            'btn_cancel' => true,
        ]);

        // Assert: redirects to families index
        $response->assertRedirect(route('families'));
    }

    #[Test]
    public function it_deletes_family()
    {
        // Arrange: create a family
        $family = \Modules\Families\Models\Family::factory()->create();

        // Act: delete the family
        $response = $this->delete(route('families.delete', ['id' => $family->id]));

        // Assert: redirects and family is deleted
        $response->assertRedirect(route('families.index'));
        $this->assertDatabaseMissing('ip_families', ['family_id' => $family->id]);
    }
}
