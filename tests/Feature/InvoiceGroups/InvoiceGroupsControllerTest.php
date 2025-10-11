<?php

namespace Tests\Feature\InvoiceGroups;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\InvoiceGroups\Controllers\InvoiceGroupsController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(InvoiceGroupsController::class)]
class InvoiceGroupsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_invoice_groups_list()
    {
        // Arrange: create invoice groups
        $invoiceGroup = \Modules\InvoiceGroups\Models\InvoiceGroup::factory()->create();

        // Act: visit invoice groups index
        $response = $this->get(route('invoice_groups.index'));

        // Assert: invoice groups are displayed
        $response->assertStatus(200);
        $response->assertSee($invoiceGroup->invoice_group_name);
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
    public function it_deletes_invoice_group()
    {
        // Arrange: create an invoice group
        $invoiceGroup = \Modules\InvoiceGroups\Models\InvoiceGroup::factory()->create();

        // Act: delete the invoice group
        $response = $this->get(route('invoice_groups.delete', ['id' => $invoiceGroup->id]));

        // Assert: redirects and invoice group is deleted
        $response->assertRedirect(route('invoice_groups'));
        $this->assertDatabaseMissing('ip_invoice_groups', ['invoice_group_id' => $invoiceGroup->id]);
    }
}
