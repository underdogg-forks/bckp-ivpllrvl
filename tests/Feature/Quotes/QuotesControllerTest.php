<?php

namespace Tests\Feature\Quotes;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Quotes\Controllers\QuotesController;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(QuotesController::class)]
class QuotesControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_redirects_to_status_all()
    {
        // Act: call the index route
        $response = $this->get(route('quotes.index'));

        // Assert: should redirect to quotes/status/all
        $response->assertRedirect(route('quotes.status', ['status' => 'all']));
    }

    #[Test]
    public function it_displays_quotes_by_status()
    {
        // Arrange: create quotes with different statuses
        $draftQuote    = \Modules\Quotes\Models\Quote::factory()->create(['status' => 'draft']);
        $sentQuote     = \Modules\Quotes\Models\Quote::factory()->create(['status' => 'sent']);
        $approvedQuote = \Modules\Quotes\Models\Quote::factory()->create(['status' => 'approved']);

        // Act: call the status route for 'draft'
        $response = $this->get(route('quotes.status', ['status' => 'draft']));
        $response->assertSee($draftQuote->title);
        $response->assertDontSee($sentQuote->title);
        $response->assertDontSee($approvedQuote->title);
        $response->assertStatus(200);

        // Act: call the status route for 'sent'
        $response = $this->get(route('quotes.status', ['status' => 'sent']));
        $response->assertSee($sentQuote->title);
        $response->assertDontSee($draftQuote->title);
        $response->assertDontSee($approvedQuote->title);
        $response->assertStatus(200);
    }
}
