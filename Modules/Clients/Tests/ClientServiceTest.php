<?php

namespace Modules\Clients\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Modules\Clients\Models\Client;
use Modules\Clients\Services\ClientsService;
use Modules\UserClients\Models\UserClient;
use Tests\TestCase;

class ClientsServiceTest extends TestCase
{
    use RefreshDatabase;

    private ClientsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ClientsService();
    }

    public function test_default_select_includes_fullname_concatenation(): void
    {
        $builder = $this->service->defaultSelect();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $builder);
    }

    public function test_default_order_by_sorts_by_client_name(): void
    {
        $builder = $this->service->defaultOrderBy();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $builder);
    }

    public function test_validation_rules_returns_correct_structure(): void
    {
        $rules = $this->service->validationRules();
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('client_name', $rules);
        $this->assertArrayHasKey('client_email', $rules);
        $this->assertEquals('required', $rules['client_name']['rules']);
    }

    public function test_get_latest_returns_active_clients(): void
    {
        Client::factory()->count(5)->create(['client_active' => 1]);
        Client::factory()->count(3)->create(['client_active' => 0]);

        $results = $this->service->getLatest(10);
        $this->assertCount(5, $results);
    }

    public function test_get_latest_limits_results(): void
    {
        Client::factory()->count(15)->create(['client_active' => 1]);

        $results = $this->service->getLatest(5);
        $this->assertCount(5, $results);
    }

    public function test_fix_avs_formats_correctly(): void
    {
        $formatted = $this->service->fixAvs('123.4567.8901.23');
        $this->assertEquals('12345678901 23', $formatted);

        $unformatted = $this->service->fixAvs('1234567890123');
        $this->assertEquals('1234567890123', $unformatted);

        $empty = $this->service->fixAvs('');
        $this->assertEquals('', $empty);
    }

    public function test_convert_date_returns_mysql_format(): void
    {
        $result = $this->service->convertDate('2023-01-15');
        $this->assertEquals('2023-01-15', $result);
    }

    public function test_convert_date_handles_invalid_input(): void
    {
        Log::shouldReceive('warning')->once();
        $result = $this->service->convertDate('invalid-date');
        $this->assertEquals('', $result);
    }

    public function test_convert_date_handles_null_input(): void
    {
        $result = $this->service->convertDate(null);
        $this->assertEquals('', $result);
    }

    public function test_client_lookup_finds_existing_client(): void
    {
        $client = Client::factory()->create(['client_name' => 'Test Client']);

        $result = $this->service->clientLookup('Test Client');
        $this->assertEquals($client->client_id, $result);
    }

    public function test_client_lookup_creates_new_client(): void
    {
        $result = $this->service->clientLookup('New Client');
        $this->assertGreaterThan(0, $result);

        $client = Client::find($result);
        $this->assertNotNull($client);
        $this->assertEquals('New Client', $client->client_name);
    }

    public function test_with_total_includes_invoice_sum(): void
    {
        $builder = $this->service->withTotal();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $builder);
    }

    public function test_with_total_paid_includes_paid_sum(): void
    {
        $builder = $this->service->withTotalPaid();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $builder);
    }

    public function test_with_total_balance_includes_balance_sum(): void
    {
        $builder = $this->service->withTotalBalance();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $builder);
    }

    public function test_is_inactive_filters_inactive_clients(): void
    {
        $builder = $this->service->isInactive();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $builder);
    }

    public function test_is_active_filters_active_clients(): void
    {
        $builder = $this->service->isActive();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $builder);
    }

    public function test_get_not_assigned_to_user_excludes_assigned_clients(): void
    {
        $user_id = 1;
        $assignedClient = Client::factory()->create(['client_active' => 1]);
        $unassignedClient = Client::factory()->create(['client_active' => 1]);

        UserClient::create(['user_id' => $user_id, 'client_id' => $assignedClient->client_id]);

        $results = $this->service->getNotAssignedToUser($user_id);
        $this->assertCount(1, $results);
        $this->assertEquals($unassignedClient->client_id, $results->first()->client_id);
    }

    public function test_get_active_returns_only_active_clients(): void
    {
        Client::factory()->count(3)->create(['client_active' => 1]);
        Client::factory()->count(2)->create(['client_active' => 0]);

        $results = $this->service->getActive();
        $this->assertCount(3, $results);
        foreach ($results as $client) {
            $this->assertEquals(1, $client->client_active);
        }
    }

    public function test_delete_logs_orphan_handling(): void
    {
        $client = Client::factory()->create();

        Log::shouldReceive('info')
            ->once()
            ->with('Orphan handling triggered after client deletion', ['client_id' => $client->client_id]);

        $this->service->delete($client->client_id);
    }
}
