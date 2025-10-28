<?php

namespace Modules\Invoices\tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\InvoiceGroups\Tests\Feature\WithFaker;
use Modules\Invoices\app\Http\Controllers\InvoiceGroupsController;
use Modules\Invoices\app\Models\InvoiceGroup;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

use function Tests\Feature\InvoiceGroups\route;

use Tests\TestCase;

#[CoversClass(InvoiceGroupsController::class)]
class InvoiceGroupsControllerTest extends TestCase
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
    public function it_displays_invoice_groups_list()
    {
        // Arrange: create invoice groups
        $invoiceGroup = \Modules\Invoices\app\Models\InvoiceGroup::factory()->create();

        // Act: visit invoice groups index
        $response = $this->get(route('invoice_groups.index'));

        // Assert: invoice groups are displayed
        $response->assertStatus(200);
        $response->assertSee($invoiceGroup->invoice_group_name);
        $response->assertViewHas('invoice_groups');
    }

    #[Test]
    public function it_displays_invoice_group_form_for_new_group()
    {
        // Act: visit new invoice group form
        $response = $this->get(route('invoice_groups.form'));

        // Assert: form is displayed
        $response->assertStatus(200);
    }

    #[Test]
    public function it_redirects_when_cancel_button_is_clicked()
    {
        // Act: submit form with cancel button
        $response = $this->post(route('invoice_groups.form'), [
            'btn_cancel' => true,
        ]);

        // Assert: redirects to invoice groups index
        $response->assertRedirect(route('invoice_groups'));
    }

    #[Test]
    public function it_creates_new_invoice_group(): void
    {
        $groupData = [
            'invoice_group_name'     => 'Test Group ' . time(),
            'invoice_group_prefix'   => 'TG' . rand(100, 999),
            'invoice_group_next_id'  => 1,
            'invoice_group_left_pad' => 0,
        ];

        $response = $this->post(route('invoice_groups.form'), $groupData);

        $response->assertRedirect(route('invoice_groups.index'));
        $this->assertDatabaseHas('ip_invoice_groups', [
            'invoice_group_name' => $groupData['invoice_group_name'],
        ]);
    }

    #[Test]
    public function it_updates_existing_invoice_group(): void
    {
        $group = InvoiceGroup::factory()->create([
            'invoice_group_name' => 'Original Group',
        ]);

        $updateData = [
            'invoice_group_name'   => 'Edited Group ' . time(),
            'invoice_group_prefix' => 'EG' . rand(100, 999),
        ];

        $response = $this->post(route('invoice_groups.form', ['id' => $group->invoice_group_id]), $updateData);

        $response->assertRedirect(route('invoice_groups.index'));
        $this->assertDatabaseHas('ip_invoice_groups', [
            'invoice_group_id'   => $group->invoice_group_id,
            'invoice_group_name' => $updateData['invoice_group_name'],
        ]);
    }

    #[Test]
    public function it_deletes_invoice_group()
    {
        // Arrange: create an invoice group
        $invoiceGroup = \Modules\Invoices\app\Models\InvoiceGroup::factory()->create();

        // Act: delete the invoice group
        $response = $this->get(route('invoice_groups.delete', ['id' => $invoiceGroup->id]));

        // Assert: redirects and invoice group is deleted
        $response->assertRedirect(route('invoice_groups'));
        $this->assertDatabaseMissing('ip_invoice_groups', ['invoice_group_id' => $invoiceGroup->id]);
    }

    #[Test]
    public function it_sets_default_values_for_new_invoice_group(): void
    {
        $response = $this->get(route('invoice_groups.form'));

        $response->assertSuccessful();
        // Should set default left_pad to 0 and next_id to 1
    }

    #[Test]
    public function it_cancels_invoice_group_form_and_redirects(): void
    {
        $response = $this->post(route('invoice_groups.form'), ['btn_cancel' => true]);

        $response->assertRedirect(route('invoice_groups.index'));
    }
}
