<?php

namespace Tests\Feature\Clients;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Modules\Clients\Models\Client;
use Modules\Clients\Models\ClientNote;
use App\Models\User;

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

    /** @test */
    public function it_redirects_index_to_active_clients_status()
    {
        $response = $this->get(route('clients.index'));

        $response->assertRedirect(route('clients.status', ['status' => 'active']));
    }

    /** @test */
    public function it_displays_only_active_clients_when_filtering_by_active_status()
    {
        $activeClient = Client::factory()->create(['active' => 1]);
        $inactiveClient = Client::factory()->create(['active' => 0]);

        $response = $this->get(route('clients.status', ['status' => 'active']));

        $response->assertSuccessful();
        $response->assertViewHas('records', function ($clients) use ($activeClient, $inactiveClient) {
            return $clients->contains($activeClient) && !$clients->contains($inactiveClient);
        });
    }

    /** @test */
    public function it_displays_only_inactive_clients_when_filtering_by_inactive_status()
    {
        $activeClient = Client::factory()->create(['active' => 1]);
        $inactiveClient = Client::factory()->create(['active' => 0]);

        $response = $this->get(route('clients.status', ['status' => 'inactive']));

        $response->assertSuccessful();
        $response->assertViewHas('records', function ($clients) use ($activeClient, $inactiveClient) {
            return !$clients->contains($activeClient) && $clients->contains($inactiveClient);
        });
    }

    /** @test */
    public function it_displays_all_clients_when_status_is_all()
    {
        $activeClient = Client::factory()->create(['active' => 1]);
        $inactiveClient = Client::factory()->create(['active' => 0]);

        $response = $this->get(route('clients.status', ['status' => 'all']));

        $response->assertSuccessful();
        $response->assertViewHas('records', function ($clients) use ($activeClient, $inactiveClient) {
            return $clients->contains($activeClient) && $clients->contains($inactiveClient);
        });
    }

    /** @test */
    public function it_cancels_client_form_and_redirects_to_index()
    {
        $response = $this->post(route('clients.form'), ['btn_cancel' => true]);

        $response->assertRedirect(route('clients.index'));
    }

    /** @test */
    public function it_displays_client_details_on_view_page()
    {
        $client = Client::factory()->create(['client_name' => 'Test Client']);

        $response = $this->get(route('clients.view', ['client_id' => $client->client_id]));

        $response->assertSuccessful();
        $response->assertViewHas('client', function ($viewClient) use ($client) {
            return $viewClient->client_id === $client->client_id;
        });
        $response->assertSee('Test Client');
    }

    /** @test */
    public function it_returns_404_when_viewing_nonexistent_client()
    {
        $response = $this->get(route('clients.view', ['client_id' => 99999]));

        $response->assertNotFound();
    }

    /** @test */
    public function it_deletes_client_and_redirects_to_index()
    {
        $client = Client::factory()->create();

        $response = $this->delete(route('clients.delete', ['client_id' => $client->client_id]));

        $response->assertRedirect(route('clients.index'));
        $this->assertDatabaseMissing('ip_clients', ['client_id' => $client->client_id]);
    }
}
