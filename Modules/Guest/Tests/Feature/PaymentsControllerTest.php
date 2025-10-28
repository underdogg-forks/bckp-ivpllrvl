<?php

namespace Modules\Guest\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use src\Controllers\PaymentsController;
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
