<?php

namespace Tests\Feature\Dashboard;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Dashboard\Controllers\DashboardController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(DashboardController::class)]
class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_dashboard_with_overview_data()
    {
        // Arrange: create sample data
        $client = \Modules\Clients\Models\Client::factory()->create();
        $invoice = \Modules\Invoices\Models\Invoice::factory()->create([
            'client_id' => $client->id,
            'total' => 1000,
        ]);

        // Act: visit dashboard
        $response = $this->get(route('dashboard'));

        // Assert: dashboard is displayed
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.index');
        $response->assertViewHas('invoice_status_totals');
        $response->assertViewHas('quote_status_totals');
    }
}
