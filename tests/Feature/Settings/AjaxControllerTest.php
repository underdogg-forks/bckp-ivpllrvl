<?php

namespace Tests\Feature\Settings;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Settings\Controllers\AjaxController;
use Tests\TestCase;

#[CoversClass(AjaxController::class)]
class AjaxControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function get_cron_key_returns_random_string()
    {
        $this->markTestIncomplete('Implement meaningful test for getCronKey');
    }
}
