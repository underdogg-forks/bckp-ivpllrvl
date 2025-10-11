<?php

namespace Tests\Feature\Invoices;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Invoices\Controllers\CronController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(CronController::class)]
class CronControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_runs_cron_for_recurring_invoices()
    {
        $this->markTestIncomplete('Implement meaningful test for cron');
    }
}
