<?php

namespace Modules\Guest\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use src\Controllers\Gateways\StripeController;
use Tests\TestCase;

#[CoversClass(StripeController::class)]
class StripeControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_handles_stripe_payment()
    {
        $this->markTestIncomplete('Implement meaningful test for payment');
    }
}
