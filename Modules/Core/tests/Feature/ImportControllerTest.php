<?php

namespace Modules\Core\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use src\Controllers\ImportController;
use Tests\TestCase;

#[CoversClass(ImportController::class)]
class ImportControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_import_page()
    {
        $this->markTestIncomplete('Implement meaningful test for index');
    }

    #[Test]
    public function it_displays_import_form()
    {
        $this->markTestIncomplete('Implement meaningful test for form');
    }

    #[Test]
    public function it_deletes_import()
    {
        $this->markTestIncomplete('Implement meaningful test for delete');
    }
}
