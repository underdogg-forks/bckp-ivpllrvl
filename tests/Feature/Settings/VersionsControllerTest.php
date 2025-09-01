<?php

namespace Tests\Feature\Settings;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Settings\Controllers\VersionsController;
use Tests\TestCase;

#[CoversClass(VersionsController::class)]
class VersionsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function index_displays_versions_list()
    {
        $this->markTestIncomplete('Implement meaningful test for index');
    }
}
