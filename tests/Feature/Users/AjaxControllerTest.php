<?php

namespace Tests\Feature\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Users\Controllers\AjaxController;
use Tests\TestCase;

#[CoversClass(AjaxController::class)]
class AjaxControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function name_query_returns_expected_users()
    {
        // Setup: create users
        // Call the controller method and assert correct users are returned
        $this->markTestIncomplete('Implement meaningful test for nameQuery');
    }

    #[Test]
    public function get_latest_returns_recent_users()
    {
        // Setup: create users
        // Call the controller method and assert recent users are returned
        $this->markTestIncomplete('Implement meaningful test for getLatest');
    }
}
