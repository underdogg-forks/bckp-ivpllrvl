<?php

namespace Tests\Feature\Mailer;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Mailer\Controllers\MailerController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(MailerController::class)]
class MailerControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function invoice_sends_email_for_invoice()
    {
        $this->markTestIncomplete('Implement meaningful test for invoice');
    }
}
