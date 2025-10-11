<?php

namespace Tests\Feature\Payments;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Payments\Controllers\PaymentsController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(PaymentsController::class)]
class PaymentsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_payments_list()
    {
        $this->markTestIncomplete('Implement meaningful test for index');
    }

    #[Test]
    public function it_displays_payment_form()
    {
        $this->markTestIncomplete('Implement meaningful test for form');
    }

    #[Test]
    public function it_deletes_payment()
    {
        $this->markTestIncomplete('Implement meaningful test for delete');
    }
}
