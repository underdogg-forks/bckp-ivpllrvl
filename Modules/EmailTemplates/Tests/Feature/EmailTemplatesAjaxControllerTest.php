<?php

namespace Modules\EmailTemplates\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\EmailTemplates\Controllers\AjaxController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(AjaxController::class)]
class EmailTemplatesAjaxControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['user_type' => 1, 'user_active' => 1]);
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_returns_email_template_content_as_json(): void
    {
        $template = EmailTemplate::factory()->create([
            'email_template_subject' => 'Test Subject',
            'email_template_body'    => 'Test Body',
        ]);

        $response = $this->post(route('email_templates.ajax.getContent'), [
            'email_template_id' => $template->email_template_id,
        ]);

        $response->assertSuccessful();
        $data = $response->json();
        $this->assertEquals('Test Subject', $data['email_template_subject']);
        $this->assertEquals('Test Body', $data['email_template_body']);
    }

    #[Test]
    public function it_returns_null_for_nonexistent_template(): void
    {
        $response = $this->post(route('email_templates.ajax.getContent'), [
            'email_template_id' => 99999,
        ]);

        $response->assertSuccessful();
        $this->assertNull($response->json());
    }
}

class FamiliesControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['user_type' => 1, 'user_active' => 1]);
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_displays_families_index(): void
    {
        $response = $this->get(route('families.index'));

        $response->assertSuccessful();
        $response->assertViewHas('families');
    }

    #[Test]
    public function it_creates_new_family(): void
    {
        $familyData = [
            'family_name' => 'Test Family',
            'is_update'   => 0,
        ];

        $response = $this->post(route('families.form'), $familyData);

        $response->assertRedirect(route('families.index'));
        $this->assertDatabaseHas('ip_families', [
            'family_name' => 'Test Family',
        ]);
    }

    #[Test]
    public function it_prevents_duplicate_family_names(): void
    {
        Family::factory()->create(['family_name' => 'Existing Family']);

        $familyData = [
            'family_name' => 'Existing Family',
            'is_update'   => 0,
        ];

        $response = $this->post(route('families.form'), $familyData);

        $response->assertRedirect(route('families.form'));
        $response->assertSessionHas('alert_error');
    }

    #[Test]
    public function it_updates_existing_family(): void
    {
        $family = Family::factory()->create(['family_name' => 'Original Family']);

        $updateData = [
            'family_name' => 'Updated Family',
        ];

        $response = $this->post(route('families.form', ['id' => $family->family_id]), $updateData);

        $response->assertRedirect(route('families.index'));
        $this->assertDatabaseHas('ip_families', [
            'family_id'   => $family->family_id,
            'family_name' => 'Updated Family',
        ]);
    }

    #[Test]
    public function it_deletes_family(): void
    {
        $family = Family::factory()->create();

        $response = $this->delete(route('families.delete', ['id' => $family->family_id]));

        $response->assertRedirect(route('families.index'));
        $this->assertDatabaseMissing('ip_families', ['family_id' => $family->family_id]);
    }
}

class UnitsControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['user_type' => 1, 'user_active' => 1]);
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_displays_units_index(): void
    {
        $response = $this->get(route('units.index'));

        $response->assertSuccessful();
        $response->assertViewHas('units');
    }

    #[Test]
    public function it_creates_new_unit(): void
    {
        $unitData = [
            'unit_name'      => 'Kilogram',
            'unit_name_plrl' => 'Kilograms',
        ];

        $response = $this->post(route('units.form'), $unitData);

        $response->assertRedirect(route('units.index'));
        $this->assertDatabaseHas('ip_units', [
            'unit_name' => 'Kilogram',
        ]);
    }

    #[Test]
    public function it_prevents_duplicate_unit_names(): void
    {
        Unit::factory()->create(['unit_name' => 'Existing Unit']);

        $unitData = [
            'unit_name'      => 'Existing Unit',
            'unit_name_plrl' => 'Existing Units',
            'is_update'      => 0,
        ];

        $response = $this->post(route('units.form'), $unitData);

        $response->assertRedirect(route('units.form'));
        $response->assertSessionHas('alert_error');
    }

    #[Test]
    public function it_updates_existing_unit(): void
    {
        $unit = Unit::factory()->create(['unit_name' => 'Original Unit']);

        $updateData = [
            'unit_name'      => 'Updated Unit',
            'unit_name_plrl' => 'Updated Units',
        ];

        $response = $this->post(route('units.form', ['id' => $unit->unit_id]), $updateData);

        $response->assertRedirect(route('units.index'));
        $this->assertDatabaseHas('ip_units', [
            'unit_id'   => $unit->unit_id,
            'unit_name' => 'Updated Unit',
        ]);
    }

    #[Test]
    public function it_deletes_unit(): void
    {
        $unit = Unit::factory()->create();

        $response = $this->delete(route('units.delete', ['id' => $unit->unit_id]));

        $response->assertRedirect(route('units.index'));
        $this->assertDatabaseMissing('ip_units', ['unit_id' => $unit->unit_id]);
    }
}
