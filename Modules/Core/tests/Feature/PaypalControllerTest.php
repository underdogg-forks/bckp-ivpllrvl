<?php

namespace Modules\Core\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Modules\Core\Controllers\Gateways\PaypalController;
use Tests\TestCase;

#[CoversClass(PaypalController::class)]
class PaypalControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_handles_paypal_payment()
    {
        $this->markTestIncomplete('Implement meaningful test for payment');
    }
}
