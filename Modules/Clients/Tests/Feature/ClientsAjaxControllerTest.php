<?php

namespace Modules\Clients\Tests\Feature;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Clients\Controllers\AjaxController;
use Modules\Users\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(AjaxController::class)]
class ClientsAjaxControllerTest extends TestCase
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
    public function it_returns_empty_json_when_query_is_empty()
    {
        $response = $this->get(route('clients.ajax.nameQuery'));

        $response->assertSuccessful();
        $response->assertJson([]);
    }

    /** @test */
    public function it_searches_clients_by_name_with_trailing_wildcard()
    {
        Client::factory()->create(['client_name' => 'John', 'client_active' => 1]);
        Client::factory()->create(['client_name' => 'Jane', 'client_active' => 1]);
        Client::factory()->create(['client_name' => 'Bob', 'client_active' => 1]);

        $response = $this->get(route('clients.ajax.nameQuery', ['query' => 'J']));

        $data = $response->json();
        $this->assertCount(2, $data);
        $this->assertTrue(str_contains($data[0]['text'], 'J'));
    }

    /** @test */
    public function it_searches_clients_with_leading_wildcard_when_permissive_search_enabled()
    {
        Client::factory()->create(['client_name' => 'John Doe', 'client_active' => 1]);
        Client::factory()->create(['client_surname' => 'Johnson', 'client_active' => 1]);

        $response = $this->get(route('clients.ajax.nameQuery', [
            'query' => 'ohn',
            'permissive_search_clients' => 1
        ]));

        $data = $response->json();
        $this->assertGreaterThanOrEqual(2, count($data));
    }

    /** @test */
    public function it_only_returns_active_clients_in_name_query()
    {
        Client::factory()->create(['client_name' => 'Active Client', 'client_active' => 1]);
        Client::factory()->create(['client_name' => 'Inactive Client', 'client_active' => 0]);

        $response = $this->get(route('clients.ajax.nameQuery', ['query' => 'Client']));

        $data = $response->json();
        $this->assertCount(1, $data);
        $this->assertStringContainsString('Active', $data[0]['text']);
    }

    /** @test */
    public function it_escapes_percent_signs_in_search_query()
    {
        Client::factory()->create(['client_name' => '100% Good', 'client_active' => 1]);

        $response = $this->get(route('clients.ajax.nameQuery', ['query' => '100%']));

        $response->assertSuccessful();
// Should not cause SQL error
    }

    /** @test */
    public function it_returns_five_most_recent_active_clients()
    {
        Client::factory()->count(10)->create(['client_active' => 1]);

        $response = $this->get(route('clients.ajax.getLatest'));

        $data = $response->json();
        $this->assertCount(5, $data);
    }

    /** @test */
    public function it_saves_permissive_search_preference_with_valid_value()
    {
        $response = $this->get(route('clients.ajax.savePreference', ['permissive_search_clients' => '1']));

        $response->assertSuccessful();
        $this->assertEquals('1', get_setting('enable_permissive_search_clients'));
    }

    /** @test */
    public function it_rejects_invalid_permissive_search_preference_value()
    {
        $response = $this->get(route('clients.ajax.savePreference', ['permissive_search_clients' => '2']));

// Should exit without saving
        $this->assertNotEquals('2', get_setting('enable_permissive_search_clients'));
    }

    /** @test */
    public function it_successfully_deletes_client_note()
    {
        $client = Client::factory()->create();
        $note = ClientNote::factory()->create(['client_id' => $client->client_id]);

        $response = $this->post(route('clients.ajax.deleteNote'), [
            'client_note_id' => $note->client_note_id
        ]);

        $response->assertJson(['success' => 1]);
        $this->assertDatabaseMissing('ip_client_notes', ['client_note_id' => $note->client_note_id]);
    }

    /** @test */
    public function it_returns_success_false_when_deleting_nonexistent_note()
    {
        $response = $this->post(route('clients.ajax.deleteNote'), [
            'client_note_id' => 99999
        ]);

        $response->assertJson(['success' => 0]);
    }

    /** @test */
    public function it_saves_client_note_with_valid_data()
    {
        $client = Client::factory()->create();

        $response = $this->post(route('clients.ajax.saveNote'), [
            'client_id' => $client->client_id,
            'client_note_content' => 'Test note content',
            csrf_token() => csrf_token()
        ]);

        $response->assertJson(['success' => 1]);
        $this->assertDatabaseHas('ip_client_notes', [
            'client_id' => $client->client_id,
            'client_note_content' => 'Test note content'
        ]);
    }

    /** @test */
    public function it_returns_validation_errors_when_saving_invalid_note()
    {
        $response = $this->post(route('clients.ajax.saveNote'), [
            'client_id' => null,
            'client_note_content' => '',
            csrf_token() => csrf_token()
        ]);

        $response->assertJson(['success' => 0]);
        $this->assertArrayHasKey('validation_errors', $response->json());
    }

    /** @test */
    public function it_returns_new_csrf_token_after_saving_note()
    {
        $client = Client::factory()->create();

        $response = $this->post(route('clients.ajax.saveNote'), [
            'client_id' => $client->client_id,
            'client_note_content' => 'Test',
            csrf_token() => csrf_token()
        ]);

        $data = $response->json();
        $this->assertArrayHasKey('new_token', $data);
        $this->assertNotEmpty($data['new_token']);
    }

    /** @test */
    public function it_loads_all_notes_for_specific_client()
    {
        $client = Client::factory()->create();
        $otherClient = Client::factory()->create();

        ClientNote::factory()->count(3)->create(['client_id' => $client->client_id]);
        ClientNote::factory()->count(2)->create(['client_id' => $otherClient->client_id]);

        $response = $this->post(route('clients.ajax.loadNotes'), [
            'client_id' => $client->client_id
        ]);

        $response->assertSuccessful();
        $response->assertViewHas('client_notes', function ($notes) {
            return count($notes) === 3;
        });
    }
}
