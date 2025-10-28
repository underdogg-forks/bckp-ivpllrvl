<?php

namespace Modules\Dashboard\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Dashboard\Controllers\DashboardController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

use function Tests\Feature\Dashboard\route;

use Tests\TestCase;

#[CoversClass(DashboardController::class)]
class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['user_type' => 1, 'user_active' => 1]);
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_displays_dashboard_with_overview_data()
    {
        // Arrange: create sample data
        $client  = \Modules\Clients\Models\tmpClient::factory()->create();
        $invoice = \Modules\Invoices\Models\Invoice::factory()->create([
            'client_id' => $client->id,
            'total'     => 1000,
        ]);

        // Act: visit dashboard
        $response = $this->get(route('dashboard'));

        // Assert: dashboard is displayed
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.index');
        $response->assertViewHas('invoice_status_totals');
        $response->assertViewHas('quote_status_totals');
    }

    #[Test]
    public function it_displays_dashboard_with_invoice_status_totals(): void
    {
        Invoice::factory()->count(5)->create(['invoice_status_id' => 1]); // Draft
        Invoice::factory()->count(3)->create(['invoice_status_id' => 2]); // Sent
        Invoice::factory()->count(7)->create(['invoice_status_id' => 4]); // Paid

        $response = $this->get(route('dashboard.index'));

        $response->assertSuccessful();
        $response->assertViewHas('invoice_status_totals');
        $response->assertViewHas('invoice_statuses');
    }

    #[Test]
    public function it_displays_dashboard_with_quote_status_totals(): void
    {
        Quote::factory()->count(4)->create(['quote_status_id' => 1]); // Draft
        Quote::factory()->count(2)->create(['quote_status_id' => 2]); // Sent
        Quote::factory()->count(3)->create(['quote_status_id' => 3]); // Approved

        $response = $this->get(route('dashboard.index'));

        $response->assertSuccessful();
        $response->assertViewHas('quote_status_totals');
        $response->assertViewHas('quote_statuses');
    }

    #[Test]
    public function it_displays_recent_invoices_on_dashboard(): void
    {
        Invoice::factory()->count(15)->create();

        $response = $this->get(route('dashboard.index'));

        $response->assertSuccessful();
        $response->assertViewHas('invoices', function ($invoices) {
            return $invoices->count() === 10; // Limited to 10
        });
    }

    #[Test]
    public function it_displays_recent_quotes_on_dashboard(): void
    {
        Quote::factory()->count(15)->create();

        $response = $this->get(route('dashboard.index'));

        $response->assertSuccessful();
        $response->assertViewHas('quotes', function ($quotes) {
            return $quotes->count() === 10; // Limited to 10
        });
    }

    #[Test]
    public function it_displays_overdue_invoices_on_dashboard(): void
    {
        Invoice::factory()->count(3)->create([
            'invoice_status_id' => 2,
            'invoice_date_due'  => now()->subDays(10),
        ]);

        $response = $this->get(route('dashboard.index'));

        $response->assertSuccessful();
        $response->assertViewHas('overdue_invoices', function ($invoices) {
            return $invoices->count() === 3;
        });
    }

    #[Test]
    public function it_displays_latest_projects_on_dashboard(): void
    {
        Project::factory()->count(5)->create();

        $response = $this->get(route('dashboard.index'));

        $response->assertSuccessful();
        $response->assertViewHas('projects');
    }

    #[Test]
    public function it_displays_latest_tasks_on_dashboard(): void
    {
        Task::factory()->count(5)->create();

        $response = $this->get(route('dashboard.index'));

        $response->assertSuccessful();
        $response->assertViewHas('tasks');
        $response->assertViewHas('task_statuses');
    }

    #[Test]
    public function it_uses_custom_invoice_overview_period_setting(): void
    {
        Setting::factory()->create([
            'setting_key'   => 'invoice_overview_period',
            'setting_value' => 'this-month',
        ]);

        $response = $this->get(route('dashboard.index'));

        $response->assertSuccessful();
        $response->assertViewHas('invoice_status_period', 'this_month');
    }

    #[Test]
    public function it_uses_custom_quote_overview_period_setting(): void
    {
        Setting::factory()->create([
            'setting_key'   => 'quote_overview_period',
            'setting_value' => 'this-quarter',
        ]);

        $response = $this->get(route('dashboard.index'));

        $response->assertSuccessful();
        $response->assertViewHas('quote_status_period', 'this_quarter');
    }
}
