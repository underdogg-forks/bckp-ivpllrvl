<?php

namespace Tests\Feature\Guest;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Guest\Controllers\GetController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(GetController::class)]
class GetControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_gets_guest_data()
    {
        $this->markTestIncomplete('Implement meaningful test for get');
    }
}
