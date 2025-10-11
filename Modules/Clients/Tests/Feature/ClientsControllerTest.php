<?php

namespace Modules\Clients\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Clients\Controllers\ClientsController;
use Modules\Clients\Models\Client;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(ClientsController::class)]
class ClientsControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['user_type' => 1]);
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_redirects_to_active_clients_status()
    {
        // Act: visit clients index
        $response = $this->get(route('clients.index'));

        // Assert: redirects to active status
        $response->assertRedirect(route('clients.status', ['status' => 'active']));
    }

    #[Test]
    public function it_displays_active_clients()
    {
        // Arrange: create active and inactive clients
        $activeClient = Client::factory()->create(['active' => 1]);
        $inactiveClient = Client::factory()->create(['active' => 0]);

        // Act: visit active clients page
        $response = $this->get(route('clients.status', ['status' => 'active']));

        // Assert: only active client is displayed
        $response->assertStatus(200);
        $response->assertViewIs('clients.index');
        $response->assertViewHas('records', function ($clients) use ($activeClient, $inactiveClient) {
            return $clients->contains($activeClient) && !$clients->contains($inactiveClient);
        });
        $response->assertSee($activeClient->name);
        $response->assertDontSee($inactiveClient->name);
    }

    #[Test]
    public function it_displays_inactive_clients()
    {
        // Arrange: create active and inactive clients
        $activeClient = Client::factory()->create(['active' => 1]);
        $inactiveClient = Client::factory()->create(['active' => 0]);

        // Act: visit inactive clients page
        $response = $this->get(route('clients.status', ['status' => 'inactive']));

        // Assert: only inactive client is displayed
        $response->assertStatus(200);
        $response->assertViewIs('clients.index');
        $response->assertViewHas('records', function ($clients) use ($activeClient, $inactiveClient) {
            return !$clients->contains($activeClient) && $clients->contains($inactiveClient);
        });
        $response->assertSee($inactiveClient->name);
        $response->assertDontSee($activeClient->name);
    }

    #[Test]
    public function it_displays_all_clients()
    {
        // Arrange: create active and inactive clients
        $activeClient = Client::factory()->create(['active' => 1]);
        $inactiveClient = Client::factory()->create(['active' => 0]);

        // Act: visit all clients page
        $response = $this->get(route('clients.status', ['status' => 'all']));

        // Assert: both clients are displayed
        $response->assertStatus(200);
        $response->assertViewIs('clients.index');
        $response->assertViewHas('records', function ($clients) use ($activeClient, $inactiveClient) {
            return $clients->contains($activeClient) && $clients->contains($inactiveClient);
        });
        $response->assertSee($activeClient->name);
        $response->assertSee($inactiveClient->name);
    }

    #[Test]
    public function it_cancels_client_form_and_redirects_to_index()
    {
        $response = $this->post(route('clients.form'), ['btn_cancel' => true]);

        $response->assertRedirect(route('clients.index'));
    }

    #[Test]
    public function it_displays_client_form_for_new_client()
    {
        // Act: visit new client form
        $response = $this->get(route('clients.form'));

        // Assert: form is displayed
        $response->assertStatus(200);
        $response->assertViewIs('clients.form');
    }

    #[Test]
    public function it_displays_client_form_for_existing_client()
    {
        // Arrange: create a client
        $client = Client::factory()->create(['client_name' => 'Test Client']);

        // Act: visit client edit form
        $response = $this->get(route('clients.view', ['client_id' => $client->client_id]));

        // Assert: form is displayed
        $response->assertStatus(200);
        $response->assertViewHas('client', function ($viewClient) use ($client) {
            return $viewClient->client_id === $client->client_id;
        });
        $response->assertSee('Test Client');
    }

    #[Test]
    public function it_redirects_when_cancel_button_is_clicked()
    {
        // Act: submit form with cancel button
        $response = $this->post(route('clients.form'), [
            'btn_cancel' => true,
        ]);

        // Assert: redirects to clients index
        $response->assertRedirect(route('clients.index'));
    }

    #[Test]
    public function it_displays_client_view()
    {
        // Arrange: create a client
        $client = Client::factory()->create();

        // Act: visit client view page
        $response = $this->get(route('clients.view', ['client_id' => $client->id]));

        // Assert: view is displayed
        $response->assertStatus(200);
        $response->assertViewIs('clients.view');
        $response->assertViewHas('client');
        $response->assertSee($client->name);
    }

    #[Test]
    public function it_returns_404_for_non_existent_client()
    {
        // Act: visit view for non-existent client
        $response = $this->get(route('clients.view', ['client_id' => 99999]));

        // Assert: 404 error
        $response->assertStatus(404);
    }

    #[Test]
    public function it_deletes_client_and_redirects_to_index()
    {
        // Arrange: create a client
        $client = Client::factory()->create();

        // Act: delete the client
        $response = $this->get(route('clients.delete', ['client_id' => $client->id]));

        // Assert: redirects and client is deleted
        $response->assertRedirect(route('clients.index'));
        $this->assertDatabaseMissing('ip_clients', ['client_id' => $client->id]);
    }
}
