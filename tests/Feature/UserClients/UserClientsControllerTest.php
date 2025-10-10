<?php

namespace Tests\Feature\UserClients;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\UserClients\Controllers\UserClientsController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(UserClientsController::class)]
class UserClientsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_redirects_to_users_from_index()
    {
        // Act: visit user clients index
        $response = $this->get(route('user_clients.index'));

        // Assert: redirects to users
        $response->assertRedirect(route('users'));
    }

    #[Test]
    public function it_displays_user_clients_for_a_user()
    {
        // Arrange: create a user
        $user = \Modules\Users\Models\User::factory()->create();

        // Act: visit user clients page
        $response = $this->get(route('user_clients.user', ['id' => $user->id]));

        // Assert: page is displayed
        $response->assertStatus(200);
        $response->assertViewIs('user_clients.new');
        $response->assertViewHas('user');
        $response->assertViewHas('user_clients');
    }

    #[Test]
    public function it_redirects_to_users_when_user_not_found()
    {
        // Act: visit user clients page for non-existent user
        $response = $this->get(route('user_clients.user', ['id' => 99999]));

        // Assert: redirects to users
        $response->assertRedirect(route('users'));
    }

    #[Test]
    public function it_redirects_to_custom_values_when_user_id_is_null()
    {
        // Act: visit create page without user_id
        $response = $this->get(route('user_clients.create'));

        // Assert: redirects to custom values
        $response->assertRedirect(route('custom_values'));
    }

    #[Test]
    public function it_deletes_user_client_and_redirects()
    {
        // Arrange: create user and user client
        $user = \Modules\Users\Models\User::factory()->create();
        $client = \Modules\Clients\Models\Client::factory()->create();
        $userClient = \Modules\UserClients\Models\UserClient::factory()->create([
            'user_id' => $user->id,
            'client_id' => $client->id,
        ]);

        // Act: delete user client
        $response = $this->get(route('user_clients.delete', ['user_client_id' => $userClient->id]));

        // Assert: redirects to user clients page
        $response->assertRedirect(route('user_clients.user', ['id' => $user->id]));
        $this->assertDatabaseMissing('ip_user_clients', ['id' => $userClient->id]);
    }
}
