<?php

namespace Modules\Projects\tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Projects\app\Http\Controllers\AjaxController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(AjaxController::class)]
class AjaxControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_task_lookup_modal()
    {
        $this->markTestIncomplete('Implement meaningful test for modalTaskLookups');
    }

    #[Test]
    public function it_processes_task_selections()
    {
        $this->markTestIncomplete('Implement meaningful test for processTaskSelections');
    }
}
