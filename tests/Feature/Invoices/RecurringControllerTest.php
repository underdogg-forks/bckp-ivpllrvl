<?php

namespace Tests\Feature\Invoices;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Invoices\Controllers\RecurringController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(RecurringController::class)]
class RecurringControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_recurring_invoices_list()
    {
        $this->markTestIncomplete('Implement meaningful test for index');
    }

    #[Test]
    public function it_displays_recurring_invoice_form()
    {
        $this->markTestIncomplete('Implement meaningful test for form');
    }

    #[Test]
    public function it_deletes_recurring_invoice()
    {
        $this->markTestIncomplete('Implement meaningful test for delete');
    }

    #[Test]
    public function it_stops_recurring_invoice()
    {
        $this->markTestIncomplete('Implement meaningful test for stop');
    }
}
