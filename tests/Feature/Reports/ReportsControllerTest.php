<?php

namespace Tests\Feature\Reports;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Reports\Controllers\ReportsController;
use Tests\TestCase;

#[CoversClass(ReportsController::class)]
class ReportsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_sales_by_client_report()
    {
        // Arrange: create clients and sales data
        $client  = \Modules\Clients\Models\Client::factory()->create();
        $invoice = \Modules\Invoices\Models\Invoice::factory()->create([
            'client_id'    => $client->id,
            'invoice_date' => now()->subDays(5),
            'total'        => 500,
        ]);

        // Act: submit the report form
        $response = $this->post(route('reports.salesByClient'), [
            'from_date'  => now()->subMonth()->format('Y-m-d'),
            'to_date'    => now()->format('Y-m-d'),
            'btn_submit' => true,
        ]);

        // Assert: report contains client and sales data
        $response->assertStatus(200);
        $response->assertSee($client->name);
        $response->assertSee('500');
    }

    #[Test]
    public function it_returns_invoices_per_client_report()
    {
        // Arrange: create clients and invoices
        $client  = \Modules\Clients\Models\Client::factory()->create();
        $invoice = \Modules\Invoices\Models\Invoice::factory()->create([
            'client_id'    => $client->id,
            'invoice_date' => now()->subDays(3),
            'total'        => 300,
        ]);

        // Act: submit the report form
        $response = $this->post(route('reports.invoicesPerClient'), [
            'from_date'  => now()->subMonth()->format('Y-m-d'),
            'to_date'    => now()->format('Y-m-d'),
            'btn_submit' => true,
        ]);

        // Assert: report contains client and invoice data
        $response->assertStatus(200);
        $response->assertSee($client->name);
        $response->assertSee('300');
    }
}
