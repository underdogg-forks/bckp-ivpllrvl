<?php

namespace Modules\Core\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Modules\Core\Controllers\Gateways\StripeController;
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
