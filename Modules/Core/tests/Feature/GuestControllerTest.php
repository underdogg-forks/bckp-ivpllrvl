<?php

namespace Modules\Core\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Modules\Core\Controllers\CustomerPortalController;
use Tests\TestCase;

#[CoversClass(CustomerPortalController::class)]
class GuestControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_guest_index()
    {
        $this->markTestIncomplete('Implement meaningful test for index');
    }
}
