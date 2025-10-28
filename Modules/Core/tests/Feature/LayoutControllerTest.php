<?php

namespace Modules\Core\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use src\LayoutController;
use Tests\TestCase;

#[CoversClass(LayoutController::class)]
class LayoutControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_buffers_view_data()
    {
        $this->markTestIncomplete('Implement meaningful test for buffer');
    }

    #[Test]
    public function it_sets_view_data()
    {
        $this->markTestIncomplete('Implement meaningful test for set');
    }

    #[Test]
    public function it_renders_layout()
    {
        $this->markTestIncomplete('Implement meaningful test for render');
    }

    #[Test]
    public function it_loads_view()
    {
        $this->markTestIncomplete('Implement meaningful test for loadView');
    }
}
