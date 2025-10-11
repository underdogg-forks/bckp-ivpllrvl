<?php

namespace Tests\Feature\EmailTemplates;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\EmailTemplates\Controllers\AjaxController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(AjaxController::class)]
class AjaxControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_gets_email_template()
    {
        $this->markTestIncomplete('Implement meaningful test for getEmailTemplate');
    }
}
