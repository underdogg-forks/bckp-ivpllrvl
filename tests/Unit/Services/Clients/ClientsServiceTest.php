<?php

namespace Tests\Unit\Services\Clients;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Clients\Models\Client;
use Modules\Clients\Services\ClientsService;
use Modules\UserClients\Models\UserClient;
use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function it_returns_all_active_clients(): void
    {
        // Arrange
        Client::create([
            'client_name' => 'Active Client 1',
            'client_active' => 1,
        ]);
        Client::create([
            'client_name' => 'Active Client 2',
            'client_active' => 1,
        ]);
        Client::create([
            'client_name' => 'Inactive Client',
            'client_active' => 0,
        ]);

        // Act
        $result = $this->service->getActive();

        // Assert
        $this->assertCount(2, $result);
        $this->assertEquals('Active Client 1', $result[0]->client_name);
        $this->assertEquals('Active Client 2', $result[1]->client_name);
        $result->each(function ($client) {
            $this->assertEquals(1, $client->client_active);
        });
    }

    #[Test]
    public function it_returns_empty_collection_when_no_active_clients_exist(): void
    {
        // Arrange
        Client::create([
            'client_name' => 'Inactive Client',
            'client_active' => 0,
        ]);

        // Act
        $result = $this->service->getActive();

        // Assert
        $this->assertCount(0, $result);
    }

    #[Test]
    public function it_returns_query_builder_for_active_clients(): void
    {
        // Act
        $builder = $this->service->isActive();

        // Assert
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $builder);
    }

    #[Test]
    public function it_returns_query_builder_for_inactive_clients(): void
    {
        // Act
        $builder = $this->service->isInactive();

        // Assert
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $builder);
    }

    #[Test]
    public function it_returns_active_clients_not_assigned_to_user(): void
    {
        // Arrange
        $userId = 1;
        
        Client::create([
            'client_name' => 'Unassigned Client',
            'client_active' => 1,
        ]);
        $client2 = Client::create([
            'client_name' => 'Assigned Client',
            'client_active' => 1,
        ]);
        Client::create([
            'client_name' => 'Inactive Client',
            'client_active' => 0,
        ]);

        // Assign client2 to the user
        UserClient::create([
            'user_id' => $userId,
            'client_id' => $client2->client_id,
        ]);

        // Act
        $result = $this->service->getNotAssignedToUser($userId);

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals('Unassigned Client', $result[0]->client_name);
    }

    #[Test]
    public function it_returns_all_active_clients_when_no_assignments_exist(): void
    {
        // Arrange
        $userId = 999;
        
        Client::create([
            'client_name' => 'Client 1',
            'client_active' => 1,
        ]);
        Client::create([
            'client_name' => 'Client 2',
            'client_active' => 1,
        ]);

        // Act
        $result = $this->service->getNotAssignedToUser($userId);

        // Assert
        $this->assertCount(2, $result);
    }
}