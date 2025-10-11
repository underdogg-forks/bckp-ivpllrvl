<?php

namespace Tests\Feature\Guest;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Guest\Controllers\GuestController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(GuestController::class)]
class GuestControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_guest_index()
    {
        $this->markTestIncomplete('Implement meaningful test for index');
    }
}
