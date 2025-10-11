<?php

namespace Tests\Feature\Guest;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Guest\Controllers\InvoicesController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(InvoicesController::class)]
class InvoicesControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_guest_invoice_view()
    {
        $this->markTestIncomplete('Implement meaningful test for view');
    }

    #[Test]
    public function it_generates_guest_invoice_pdf()
    {
        $this->markTestIncomplete('Implement meaningful test for generatePdf');
    }
}
