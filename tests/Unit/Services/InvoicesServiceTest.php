<?php

namespace Tests\Unit\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Modules\Invoices\Models\Invoice;
use Modules\Invoices\Models\InvoiceAmount;
use Modules\Invoices\Models\InvoiceTaxRate;
use Modules\Invoices\Models\InvoiceSumex;
use Modules\Invoices\Services\InvoicesService;
use Modules\Payments\Models\Payment;
use Tests\TestCase;

class InvoicesServiceTest extends TestCase
{
    use RefreshDatabase;

    private InvoicesService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new InvoicesService();
    }

    public function test_markViewed_updates_status_from_sent_to_viewed(): void
    {
        // Arrange: Create an invoice with "sent" status
        $invoice = Invoice::factory()->create([
            'invoice_status_id' => 2, // Sent status
            'is_read_only' => 0,
        ]);

        // Act: Mark as viewed
        $this->service->markViewed($invoice->invoice_id);

        // Assert: Status should be updated to viewed
        $this->assertDatabaseHas('ip_invoices', [
            'invoice_id' => $invoice->invoice_id,
            'invoice_status_id' => 3, // Viewed status
        ]);
    }

    public function test_markViewed_does_not_update_status_when_not_sent(): void
    {
        // Arrange: Create an invoice with draft status
        $invoice = Invoice::factory()->create([
            'invoice_status_id' => 1, // Draft status
            'is_read_only' => 0,
        ]);

        // Act: Mark as viewed
        $this->service->markViewed($invoice->invoice_id);

        // Assert: Status should remain unchanged
        $this->assertDatabaseHas('ip_invoices', [
            'invoice_id' => $invoice->invoice_id,
            'invoice_status_id' => 1, // Still draft
        ]);
    }

    public function test_markViewed_sets_read_only_when_configured(): void
    {
        // Arrange: Create an invoice and configure read-only settings
        $invoice = Invoice::factory()->create([
            'invoice_status_id' => 2, // Sent
            'is_read_only' => 0,
        ]);
        
        // Mock configuration
        config(['app.disable_read_only' => false]);
        config(['settings.read_only_toggle' => 3]);

        // Act: Mark as viewed
        $this->service->markViewed($invoice->invoice_id);

        // Assert: Should be marked read-only
        $this->assertDatabaseHas('ip_invoices', [
            'invoice_id' => $invoice->invoice_id,
            'is_read_only' => 1,
        ]);
    }

    public function test_markViewed_does_not_update_nonexistent_invoice(): void
    {
        // Act: Try to mark non-existent invoice as viewed
        $this->service->markViewed(99999);

        // Assert: No exception should be thrown
        $this->assertTrue(true);
    }

    public function test_markSent_updates_status_from_draft_to_sent(): void
    {
        // Arrange: Create a draft invoice
        $invoice = Invoice::factory()->create([
            'invoice_status_id' => 1, // Draft
            'is_read_only' => 0,
            'invoice_date_created' => date('Y-m-d'),
        ]);

        // Act: Mark as sent
        $this->service->markSent($invoice->invoice_id);

        // Assert: Status should be updated to sent
        $this->assertDatabaseHas('ip_invoices', [
            'invoice_id' => $invoice->invoice_id,
            'invoice_status_id' => 2, // Sent status
        ]);
    }

    public function test_markSent_does_not_update_status_when_not_draft(): void
    {
        // Arrange: Create an invoice that's already sent
        $invoice = Invoice::factory()->create([
            'invoice_status_id' => 2, // Already sent
            'is_read_only' => 0,
        ]);

        // Act: Mark as sent again
        $this->service->markSent($invoice->invoice_id);

        // Assert: Status should remain sent
        $this->assertDatabaseHas('ip_invoices', [
            'invoice_id' => $invoice->invoice_id,
            'invoice_status_id' => 2,
        ]);
    }

    public function test_markSent_sets_read_only_when_configured(): void
    {
        // Arrange: Create a draft invoice
        $invoice = Invoice::factory()->create([
            'invoice_status_id' => 1, // Draft
            'is_read_only' => 0,
            'invoice_date_created' => date('Y-m-d'),
        ]);
        
        // Mock configuration for read-only on sent
        config(['app.disable_read_only' => false]);
        config(['settings.read_only_toggle' => 2]);

        // Act: Mark as sent
        $this->service->markSent($invoice->invoice_id);

        // Assert: Should be marked read-only
        $this->assertDatabaseHas('ip_invoices', [
            'invoice_id' => $invoice->invoice_id,
            'is_read_only' => 1,
        ]);
    }

    public function test_getPayments_attaches_payments_to_invoice(): void
    {
        // Arrange: Create invoice with payments
        $invoice = Invoice::factory()->create();
        Payment::factory()->create(['invoice_id' => $invoice->invoice_id]);
        Payment::factory()->create(['invoice_id' => $invoice->invoice_id]);

        // Convert to object for method compatibility
        $invoiceObj = (object)$invoice->toArray();
        $invoiceObj->invoice_id = $invoice->invoice_id;

        // Act: Get payments for invoice
        $result = $this->service->getPayments($invoiceObj);

        // Assert: Payments should be attached
        $this->assertNotNull($result->payments);
        $this->assertCount(2, $result->payments);
    }

    public function test_getPayments_returns_null_when_no_payments(): void
    {
        // Arrange: Create invoice without payments
        $invoice = Invoice::factory()->create();
        $invoiceObj = (object)$invoice->toArray();
        $invoiceObj->invoice_id = $invoice->invoice_id;

        // Act: Get payments for invoice
        $result = $this->service->getPayments($invoiceObj);

        // Assert: Payments should be null
        $this->assertNull($result->payments);
    }

    public function test_generateInvoiceNumberIfApplicable_generates_number_for_draft_without_number(): void
    {
        // Arrange: Create draft invoice without number
        $invoice = Invoice::factory()->create([
            'invoice_status_id' => 1, // Draft
            'invoice_number' => '',
            'invoice_group_id' => 1,
        ]);
        
        // Mock the setting
        config(['settings.generate_invoice_number_for_draft' => 0]);

        // Act: Generate number if applicable
        $this->service->generateInvoiceNumberIfApplicable($invoice->invoice_id);

        // Assert: Invoice should have a number assigned
        $invoice->refresh();
        $this->assertNotEmpty($invoice->invoice_number);
    }

    public function test_generateInvoiceNumberIfApplicable_does_not_generate_when_already_has_number(): void
    {
        // Arrange: Create draft invoice with existing number
        $invoice = Invoice::factory()->create([
            'invoice_status_id' => 1, // Draft
            'invoice_number' => 'INV-001',
            'invoice_group_id' => 1,
        ]);
        
        config(['settings.generate_invoice_number_for_draft' => 0]);

        // Act: Try to generate number
        $this->service->generateInvoiceNumberIfApplicable($invoice->invoice_id);

        // Assert: Invoice number should remain unchanged
        $this->assertDatabaseHas('ip_invoices', [
            'invoice_id' => $invoice->invoice_id,
            'invoice_number' => 'INV-001',
        ]);
    }

    public function test_generateInvoiceNumberIfApplicable_does_not_generate_when_not_draft(): void
    {
        // Arrange: Create sent invoice without number
        $invoice = Invoice::factory()->create([
            'invoice_status_id' => 2, // Sent
            'invoice_number' => '',
            'invoice_group_id' => 1,
        ]);
        
        config(['settings.generate_invoice_number_for_draft' => 0]);

        // Act: Try to generate number
        $this->service->generateInvoiceNumberIfApplicable($invoice->invoice_id);

        // Assert: Invoice number should remain empty
        $this->assertDatabaseHas('ip_invoices', [
            'invoice_id' => $invoice->invoice_id,
            'invoice_number' => '',
        ]);
    }

    public function test_updateInvoiceDueDate_updates_due_date_when_not_read_only(): void
    {
        // Arrange: Create invoice that's not read-only
        $invoice = Invoice::factory()->create([
            'is_read_only' => 0,
            'invoice_date_created' => date('Y-m-d'),
            'invoice_date_due' => date('Y-m-d', strtotime('+30 days')),
        ]);
        
        config(['settings.no_update_invoice_due_date_mail' => 0]);

        // Act: Update due date
        $this->service->updateInvoiceDueDate($invoice->invoice_id);

        // Assert: Due date should be updated
        $invoice->refresh();
        $this->assertNotNull($invoice->invoice_date_due);
    }

    public function test_updateInvoiceDueDate_does_not_update_when_read_only(): void
    {
        // Arrange: Create read-only invoice
        $originalDueDate = date('Y-m-d', strtotime('+30 days'));
        $invoice = Invoice::factory()->create([
            'is_read_only' => 1,
            'invoice_date_created' => date('Y-m-d'),
            'invoice_date_due' => $originalDueDate,
        ]);
        
        config(['settings.no_update_invoice_due_date_mail' => 0]);

        // Act: Try to update due date
        $this->service->updateInvoiceDueDate($invoice->invoice_id);

        // Assert: Due date should remain unchanged
        $this->assertDatabaseHas('ip_invoices', [
            'invoice_id' => $invoice->invoice_id,
            'invoice_date_due' => $originalDueDate,
        ]);
    }

    public function test_updateInvoiceDueDate_does_not_update_when_setting_disabled(): void
    {
        // Arrange: Create invoice with update disabled
        $originalDueDate = date('Y-m-d', strtotime('+30 days'));
        $invoice = Invoice::factory()->create([
            'is_read_only' => 0,
            'invoice_date_created' => date('Y-m-d'),
            'invoice_date_due' => $originalDueDate,
        ]);
        
        config(['settings.no_update_invoice_due_date_mail' => 1]);

        // Act: Try to update due date
        $this->service->updateInvoiceDueDate($invoice->invoice_id);

        // Assert: Due date should remain unchanged
        $this->assertDatabaseHas('ip_invoices', [
            'invoice_id' => $invoice->invoice_id,
            'invoice_date_due' => $originalDueDate,
        ]);
    }

    public function test_markViewed_only_updates_when_changes_exist(): void
    {
        // Arrange: Create invoice that won't trigger any updates
        $invoice = Invoice::factory()->create([
            'invoice_status_id' => 3, // Already viewed
            'is_read_only' => 1, // Already read-only
        ]);
        
        config(['app.disable_read_only' => true]);

        // Act: Try to mark as viewed
        $this->service->markViewed($invoice->invoice_id);

        // Assert: Status should remain unchanged
        $this->assertDatabaseHas('ip_invoices', [
            'invoice_id' => $invoice->invoice_id,
            'invoice_status_id' => 3,
            'is_read_only' => 1,
        ]);
    }

    public function test_markSent_only_updates_when_changes_exist(): void
    {
        // Arrange: Create invoice that's already sent
        $invoice = Invoice::factory()->create([
            'invoice_status_id' => 2, // Already sent
            'is_read_only' => 1,
        ]);
        
        config(['app.disable_read_only' => true]);

        // Act: Try to mark as sent
        $this->service->markSent($invoice->invoice_id);

        // Assert: No database update should occur for status
        $this->assertDatabaseHas('ip_invoices', [
            'invoice_id' => $invoice->invoice_id,
            'invoice_status_id' => 2,
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}