<?php

namespace Modules\Guest\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use src\Controllers\QuotesController;
use Tests\TestCase;

#[CoversClass(QuotesController::class)]
class QuotesControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_guest_quote_view()
    {
        $this->markTestIncomplete('Implement meaningful test for view');
    }

    #[Test]
    public function it_generates_guest_quote_pdf()
    {
        $this->markTestIncomplete('Implement meaningful test for generatePdf');
    }

    #[Test]
    public function it_approves_guest_quote()
    {
        $this->markTestIncomplete('Implement meaningful test for approve');
    }

    #[Test]
    public function it_rejects_guest_quote()
    {
        $this->markTestIncomplete('Implement meaningful test for reject');
    }
}
