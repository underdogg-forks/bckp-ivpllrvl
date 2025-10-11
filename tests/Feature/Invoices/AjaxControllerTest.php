<?php

namespace Tests\Feature\Invoices;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Invoices\Controllers\AjaxController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(AjaxController::class)]
class AjaxControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_saves_invoice_item()
    {
        $this->markTestIncomplete('Implement meaningful test for save');
    }

    #[Test]
    public function it_saves_invoice_tax_rate()
    {
        $this->markTestIncomplete('Implement meaningful test for saveInvoiceTaxRate');
    }

    #[Test]
    public function it_deletes_invoice_item()
    {
        $this->markTestIncomplete('Implement meaningful test for deleteItem');
    }

    #[Test]
    public function it_gets_invoice_item()
    {
        $this->markTestIncomplete('Implement meaningful test for getItem');
    }

    #[Test]
    public function it_displays_copy_invoice_modal()
    {
        $this->markTestIncomplete('Implement meaningful test for modalCopyInvoice');
    }

    #[Test]
    public function it_copies_invoice()
    {
        $this->markTestIncomplete('Implement meaningful test for copyInvoice');
    }

    #[Test]
    public function it_displays_create_invoice_modal()
    {
        $this->markTestIncomplete('Implement meaningful test for modalCreateInvoice');
    }

    #[Test]
    public function it_creates_invoice()
    {
        $this->markTestIncomplete('Implement meaningful test for create');
    }

    #[Test]
    public function it_displays_change_client_modal()
    {
        $this->markTestIncomplete('Implement meaningful test for modalChangeClient');
    }

    #[Test]
    public function it_changes_invoice_client()
    {
        $this->markTestIncomplete('Implement meaningful test for changeClient');
    }

    #[Test]
    public function it_displays_add_invoice_tax_modal()
    {
        $this->markTestIncomplete('Implement meaningful test for modalAddInvoiceTax');
    }
}
