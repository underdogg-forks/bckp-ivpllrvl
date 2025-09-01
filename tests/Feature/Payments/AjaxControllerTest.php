<?php

namespace Tests\Feature\Payments;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Payments\Controllers\AjaxController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(AjaxController::class)]
class AjaxControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function add_creates_payment_and_returns_success()
    {
        $this->markTestIncomplete('Implement meaningful test for add');
    }

    #[Test]
    public function modal_add_payment_displays_payment_modal()
    {
        $this->markTestIncomplete('Implement meaningful test for modalAddPayment');
    }
}
