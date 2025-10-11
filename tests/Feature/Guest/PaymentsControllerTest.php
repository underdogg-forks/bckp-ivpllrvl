<?php

namespace Tests\Feature\Guest;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Guest\Controllers\PaymentsController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(PaymentsController::class)]
class PaymentsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_guest_payment_form()
    {
        $this->markTestIncomplete('Implement meaningful test for form');
    }
}
