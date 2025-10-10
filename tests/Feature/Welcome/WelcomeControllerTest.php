<?php

namespace Tests\Feature\Welcome;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Welcome\Controllers\WelcomeController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(WelcomeController::class)]
class WelcomeControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_welcome_page()
    {
        // Act: visit the welcome page
        $response = $this->get(route('welcome'));

        // Assert: page is displayed successfully
        $response->assertStatus(200);
        $response->assertViewIs('welcome');
    }
}
