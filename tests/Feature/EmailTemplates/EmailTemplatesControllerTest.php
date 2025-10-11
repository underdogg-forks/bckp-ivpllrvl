<?php

namespace Tests\Feature\EmailTemplates;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\EmailTemplates\Controllers\EmailTemplatesController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(EmailTemplatesController::class)]
class EmailTemplatesControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_email_templates_list()
    {
        $this->markTestIncomplete('Implement meaningful test for index');
    }

    #[Test]
    public function it_displays_email_template_form()
    {
        $this->markTestIncomplete('Implement meaningful test for form');
    }

    #[Test]
    public function it_deletes_email_template()
    {
        $this->markTestIncomplete('Implement meaningful test for delete');
    }
}
