<?php

namespace Tests\Feature\Invoices;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Invoices\Controllers\InvoicesController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(InvoicesController::class)]
class InvoicesControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_redirects_to_draft_invoices_from_index()
    {
        $this->markTestIncomplete('Implement meaningful test for index');
    }

    #[Test]
    public function it_displays_invoices_by_status()
    {
        $this->markTestIncomplete('Implement meaningful test for status');
    }

    #[Test]
    public function it_displays_archived_invoices()
    {
        $this->markTestIncomplete('Implement meaningful test for archive');
    }

    #[Test]
    public function it_downloads_invoice_pdf()
    {
        $this->markTestIncomplete('Implement meaningful test for download');
    }

    #[Test]
    public function it_displays_invoice_view()
    {
        $this->markTestIncomplete('Implement meaningful test for view');
    }

    #[Test]
    public function it_deletes_invoice()
    {
        $this->markTestIncomplete('Implement meaningful test for delete');
    }

    #[Test]
    public function it_generates_invoice_pdf()
    {
        $this->markTestIncomplete('Implement meaningful test for generatePdf');
    }

    #[Test]
    public function it_generates_invoice_xml()
    {
        $this->markTestIncomplete('Implement meaningful test for generateXml');
    }

    #[Test]
    public function it_generates_sumex_pdf()
    {
        $this->markTestIncomplete('Implement meaningful test for generateSumexPdf');
    }

    #[Test]
    public function it_generates_sumex_copy()
    {
        $this->markTestIncomplete('Implement meaningful test for generateSumexCopy');
    }

    #[Test]
    public function it_deletes_invoice_tax()
    {
        $this->markTestIncomplete('Implement meaningful test for deleteInvoiceTax');
    }

    #[Test]
    public function it_recalculates_all_invoices()
    {
        $this->markTestIncomplete('Implement meaningful test for recalculateAllInvoices');
    }
}
