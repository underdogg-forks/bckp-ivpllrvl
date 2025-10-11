<?php

namespace Tests\Unit\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_isActive_returns_active_clients_query(): void
    {
        // Arrange: Create active and inactive clients
        Client::factory()->create(['client_active' => 1, 'client_name' => 'Active Client 1']);
        Client::factory()->create(['client_active' => 1, 'client_name' => 'Active Client 2']);
        Client::factory()->create(['client_active' => 0, 'client_name' => 'Inactive Client']);

        // Act: Get active clients
        $activeClients = $this->service->isActive()->get();

        // Assert: Should only return active clients
        $this->assertCount(2, $activeClients);
        $this->assertTrue($activeClients->every(fn($client) => $client->client_active === 1));
    }

    public function test_isInactive_returns_inactive_clients_query(): void
    {
        // Arrange: Create active and inactive clients
        Client::factory()->create(['client_active' => 1, 'client_name' => 'Active Client']);
        Client::factory()->create(['client_active' => 0, 'client_name' => 'Inactive Client 1']);
        Client::factory()->create(['client_active' => 0, 'client_name' => 'Inactive Client 2']);

        // Act: Get inactive clients
        $inactiveClients = $this->service->isInactive()->get();

        // Assert: Should only return inactive clients
        $this->assertCount(2, $inactiveClients);
        $this->assertTrue($inactiveClients->every(fn($client) => $client->client_active === 0));
    }

    public function test_getNotAssignedToUser_returns_unassigned_active_clients(): void
    {
        // Arrange: Create clients and user assignments
        $userId = 1;
        $assignedClient = Client::factory()->create(['client_active' => 1, 'client_name' => 'Assigned Client']);
        $unassignedClient1 = Client::factory()->create(['client_active' => 1, 'client_name' => 'Unassigned Client 1']);
        $unassignedClient2 = Client::factory()->create(['client_active' => 1, 'client_name' => 'Unassigned Client 2']);
        $inactiveClient = Client::factory()->create(['client_active' => 0, 'client_name' => 'Inactive Client']);

        // Assign one client to the user
        UserClient::factory()->create([
            'user_id' => $userId,
            'client_id' => $assignedClient->client_id,
        ]);

        // Act: Get unassigned clients for user
        $result = $this->service->getNotAssignedToUser($userId);

        // Assert: Should return only unassigned active clients
        $this->assertCount(2, $result);
        $this->assertFalse($result->contains('client_id', $assignedClient->client_id));
        $this->assertTrue($result->contains('client_id', $unassignedClient1->client_id));
        $this->assertTrue($result->contains('client_id', $unassignedClient2->client_id));
        $this->assertFalse($result->contains('client_id', $inactiveClient->client_id));
    }

    public function test_getNotAssignedToUser_returns_all_active_when_no_assignments(): void
    {
        // Arrange: Create clients but no assignments
        $userId = 999;
        Client::factory()->create(['client_active' => 1, 'client_name' => 'Client 1']);
        Client::factory()->create(['client_active' => 1, 'client_name' => 'Client 2']);
        Client::factory()->create(['client_active' => 0, 'client_name' => 'Inactive Client']);

        // Act: Get unassigned clients
        $result = $this->service->getNotAssignedToUser($userId);

        // Assert: Should return all active clients
        $this->assertCount(2, $result);
        $this->assertTrue($result->every(fn($client) => $client->client_active === 1));
    }

    public function test_getActive_returns_all_active_clients(): void
    {
        // Arrange: Create mix of active and inactive clients
        Client::factory()->create(['client_active' => 1, 'client_name' => 'Active Client 1']);
        Client::factory()->create(['client_active' => 1, 'client_name' => 'Active Client 2']);
        Client::factory()->create(['client_active' => 1, 'client_name' => 'Active Client 3']);
        Client::factory()->create(['client_active' => 0, 'client_name' => 'Inactive Client 1']);
        Client::factory()->create(['client_active' => 0, 'client_name' => 'Inactive Client 2']);

        // Act: Get all active clients
        $result = $this->service->getActive();

        // Assert: Should return only active clients
        $this->assertCount(3, $result);
        $this->assertTrue($result->every(fn($client) => $client->client_active === 1));
    }

    public function test_getActive_returns_empty_collection_when_no_active_clients(): void
    {
        // Arrange: Create only inactive clients
        Client::factory()->create(['client_active' => 0, 'client_name' => 'Inactive Client 1']);
        Client::factory()->create(['client_active' => 0, 'client_name' => 'Inactive Client 2']);

        // Act: Get active clients
        $result = $this->service->getActive();

        // Assert: Should return empty collection
        $this->assertCount(0, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

    public function test_getActive_returns_correct_collection_type(): void
    {
        // Arrange: Create an active client
        Client::factory()->create(['client_active' => 1, 'client_name' => 'Test Client']);

        // Act: Get active clients
        $result = $this->service->getActive();

        // Assert: Should return Eloquent Collection of Client models
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
        $this->assertContainsOnlyInstancesOf(Client::class, $result);
    }

    public function test_getNotAssignedToUser_handles_multiple_users_correctly(): void
    {
        // Arrange: Create clients and assignments for different users
        $user1Id = 1;
        $user2Id = 2;
        $client1 = Client::factory()->create(['client_active' => 1, 'client_name' => 'Client 1']);
        $client2 = Client::factory()->create(['client_active' => 1, 'client_name' => 'Client 2']);
        $client3 = Client::factory()->create(['client_active' => 1, 'client_name' => 'Client 3']);

        UserClient::factory()->create(['user_id' => $user1Id, 'client_id' => $client1->client_id]);
        UserClient::factory()->create(['user_id' => $user2Id, 'client_id' => $client2->client_id]);

        // Act: Get unassigned clients for user 1
        $resultUser1 = $this->service->getNotAssignedToUser($user1Id);

        // Assert: User 1 should not see client1, but should see client2 and client3
        $this->assertCount(2, $resultUser1);
        $this->assertFalse($resultUser1->contains('client_id', $client1->client_id));
        $this->assertTrue($resultUser1->contains('client_id', $client2->client_id));
        $this->assertTrue($resultUser1->contains('client_id', $client3->client_id));

        // Act: Get unassigned clients for user 2
        $resultUser2 = $this->service->getNotAssignedToUser($user2Id);

        // Assert: User 2 should not see client2, but should see client1 and client3
        $this->assertCount(2, $resultUser2);
        $this->assertTrue($resultUser2->contains('client_id', $client1->client_id));
        $this->assertFalse($resultUser2->contains('client_id', $client2->client_id));
        $this->assertTrue($resultUser2->contains('client_id', $client3->client_id));
    }
}