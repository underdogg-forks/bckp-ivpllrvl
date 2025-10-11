<?php

namespace Tests\Unit\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Quotes\Models\Quote;
use Modules\Quotes\Services\QuotesService;
use Tests\TestCase;

class QuotesServiceTest extends TestCase
{
    use RefreshDatabase;

    private QuotesService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new QuotesService();
    }

    public function test_approveQuoteByKey_approves_sent_quote(): void
    {
        // Arrange: Create a sent quote
        $quote = Quote::factory()->create([
            'quote_status_id' => 2, // Sent
            'quote_url_key' => 'test-key-123',
        ]);

        // Act: Approve by key
        $this->service->approveQuoteByKey('test-key-123');

        // Assert: Should be approved
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id' => $quote->quote_id,
            'quote_status_id' => 4, // Approved
        ]);
    }

    public function test_approveQuoteByKey_approves_viewed_quote(): void
    {
        // Arrange: Create a viewed quote
        $quote = Quote::factory()->create([
            'quote_status_id' => 3, // Viewed
            'quote_url_key' => 'test-key-456',
        ]);

        // Act: Approve by key
        $this->service->approveQuoteByKey('test-key-456');

        // Assert: Should be approved
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id' => $quote->quote_id,
            'quote_status_id' => 4, // Approved
        ]);
    }

    public function test_approveQuoteByKey_does_not_approve_draft_quote(): void
    {
        // Arrange: Create a draft quote
        $quote = Quote::factory()->create([
            'quote_status_id' => 1, // Draft
            'quote_url_key' => 'test-key-draft',
        ]);

        // Act: Try to approve by key
        $this->service->approveQuoteByKey('test-key-draft');

        // Assert: Should remain draft
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id' => $quote->quote_id,
            'quote_status_id' => 1, // Still draft
        ]);
    }

    public function test_approveQuoteByKey_does_not_approve_already_approved_quote(): void
    {
        // Arrange: Create an already approved quote
        $quote = Quote::factory()->create([
            'quote_status_id' => 4, // Already approved
            'quote_url_key' => 'test-key-approved',
        ]);

        // Act: Try to approve again
        $this->service->approveQuoteByKey('test-key-approved');

        // Assert: Should remain approved
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id' => $quote->quote_id,
            'quote_status_id' => 4,
        ]);
    }

    public function test_rejectQuoteByKey_rejects_sent_quote(): void
    {
        // Arrange: Create a sent quote
        $quote = Quote::factory()->create([
            'quote_status_id' => 2, // Sent
            'quote_url_key' => 'reject-key-123',
        ]);

        // Act: Reject by key
        $this->service->rejectQuoteByKey('reject-key-123');

        // Assert: Should be rejected
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id' => $quote->quote_id,
            'quote_status_id' => 5, // Rejected
        ]);
    }

    public function test_rejectQuoteByKey_rejects_viewed_quote(): void
    {
        // Arrange: Create a viewed quote
        $quote = Quote::factory()->create([
            'quote_status_id' => 3, // Viewed
            'quote_url_key' => 'reject-key-456',
        ]);

        // Act: Reject by key
        $this->service->rejectQuoteByKey('reject-key-456');

        // Assert: Should be rejected
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id' => $quote->quote_id,
            'quote_status_id' => 5, // Rejected
        ]);
    }

    public function test_rejectQuoteByKey_does_not_reject_draft_quote(): void
    {
        // Arrange: Create a draft quote
        $quote = Quote::factory()->create([
            'quote_status_id' => 1, // Draft
            'quote_url_key' => 'reject-key-draft',
        ]);

        // Act: Try to reject by key
        $this->service->rejectQuoteByKey('reject-key-draft');

        // Assert: Should remain draft
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id' => $quote->quote_id,
            'quote_status_id' => 1, // Still draft
        ]);
    }

    public function test_approveQuoteById_approves_sent_quote(): void
    {
        // Arrange: Create a sent quote
        $quote = Quote::factory()->create([
            'quote_status_id' => 2, // Sent
        ]);

        // Act: Approve by ID
        $this->service->approveQuoteById($quote->quote_id);

        // Assert: Should be approved
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id' => $quote->quote_id,
            'quote_status_id' => 4, // Approved
        ]);
    }

    public function test_approveQuoteById_approves_viewed_quote(): void
    {
        // Arrange: Create a viewed quote
        $quote = Quote::factory()->create([
            'quote_status_id' => 3, // Viewed
        ]);

        // Act: Approve by ID
        $this->service->approveQuoteById($quote->quote_id);

        // Assert: Should be approved
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id' => $quote->quote_id,
            'quote_status_id' => 4, // Approved
        ]);
    }

    public function test_approveQuoteById_does_not_approve_draft_quote(): void
    {
        // Arrange: Create a draft quote
        $quote = Quote::factory()->create([
            'quote_status_id' => 1, // Draft
        ]);

        // Act: Try to approve by ID
        $this->service->approveQuoteById($quote->quote_id);

        // Assert: Should remain draft
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id' => $quote->quote_id,
            'quote_status_id' => 1, // Still draft
        ]);
    }

    public function test_rejectQuoteById_rejects_sent_quote(): void
    {
        // Arrange: Create a sent quote
        $quote = Quote::factory()->create([
            'quote_status_id' => 2, // Sent
        ]);

        // Act: Reject by ID
        $this->service->rejectQuoteById($quote->quote_id);

        // Assert: Should be rejected
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id' => $quote->quote_id,
            'quote_status_id' => 5, // Rejected
        ]);
    }

    public function test_rejectQuoteById_rejects_viewed_quote(): void
    {
        // Arrange: Create a viewed quote
        $quote = Quote::factory()->create([
            'quote_status_id' => 3, // Viewed
        ]);

        // Act: Reject by ID
        $this->service->rejectQuoteById($quote->quote_id);

        // Assert: Should be rejected
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id' => $quote->quote_id,
            'quote_status_id' => 5, // Rejected
        ]);
    }

    public function test_rejectQuoteById_does_not_reject_draft_quote(): void
    {
        // Arrange: Create a draft quote
        $quote = Quote::factory()->create([
            'quote_status_id' => 1, // Draft
        ]);

        // Act: Try to reject by ID
        $this->service->rejectQuoteById($quote->quote_id);

        // Assert: Should remain draft
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id' => $quote->quote_id,
            'quote_status_id' => 1, // Still draft
        ]);
    }

    public function test_markViewed_changes_sent_quote_to_viewed(): void
    {
        // Arrange: Create a sent quote
        $quote = Quote::factory()->create([
            'quote_status_id' => 2, // Sent
        ]);

        // Act: Mark as viewed
        $this->service->markViewed($quote->quote_id);

        // Assert: Should be viewed
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id' => $quote->quote_id,
            'quote_status_id' => 3, // Viewed
        ]);
    }

    public function test_markViewed_does_not_change_draft_quote(): void
    {
        // Arrange: Create a draft quote
        $quote = Quote::factory()->create([
            'quote_status_id' => 1, // Draft
        ]);

        // Act: Try to mark as viewed
        $this->service->markViewed($quote->quote_id);

        // Assert: Should remain draft
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id' => $quote->quote_id,
            'quote_status_id' => 1, // Still draft
        ]);
    }

    public function test_markViewed_does_not_change_already_viewed_quote(): void
    {
        // Arrange: Create an already viewed quote
        $quote = Quote::factory()->create([
            'quote_status_id' => 3, // Already viewed
        ]);

        // Act: Try to mark as viewed again
        $this->service->markViewed($quote->quote_id);

        // Assert: Should remain viewed
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id' => $quote->quote_id,
            'quote_status_id' => 3,
        ]);
    }

    public function test_markSent_changes_draft_quote_to_sent(): void
    {
        // Arrange: Create a draft quote
        $quote = Quote::factory()->create([
            'quote_status_id' => 1, // Draft
        ]);

        // Act: Mark as sent
        $this->service->markSent($quote->quote_id);

        // Assert: Should be sent
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id' => $quote->quote_id,
            'quote_status_id' => 2, // Sent
        ]);
    }

    public function test_markSent_does_not_change_already_sent_quote(): void
    {
        // Arrange: Create an already sent quote
        $quote = Quote::factory()->create([
            'quote_status_id' => 2, // Already sent
        ]);

        // Act: Try to mark as sent again
        $this->service->markSent($quote->quote_id);

        // Assert: Should remain sent
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id' => $quote->quote_id,
            'quote_status_id' => 2,
        ]);
    }

    public function test_markSent_does_not_change_viewed_quote(): void
    {
        // Arrange: Create a viewed quote
        $quote = Quote::factory()->create([
            'quote_status_id' => 3, // Viewed
        ]);

        // Act: Try to mark as sent
        $this->service->markSent($quote->quote_id);

        // Assert: Should remain viewed
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id' => $quote->quote_id,
            'quote_status_id' => 3, // Still viewed
        ]);
    }

    public function test_generateQuoteNumberIfApplicable_generates_number_for_draft_without_number(): void
    {
        // Arrange: Create draft quote without number
        $quote = Quote::factory()->create([
            'quote_status_id' => 1, // Draft
            'quote_number' => '',
            'invoice_group_id' => 1,
        ]);
        
        config(['settings.generate_quote_number_for_draft' => 0]);

        // Act: Generate number if applicable
        $this->service->generateQuoteNumberIfApplicable($quote->quote_id);

        // Assert: Quote should have a number assigned
        $quote->refresh();
        $this->assertNotEmpty($quote->quote_number);
    }

    public function test_generateQuoteNumberIfApplicable_does_not_generate_when_already_has_number(): void
    {
        // Arrange: Create draft quote with existing number
        $quote = Quote::factory()->create([
            'quote_status_id' => 1, // Draft
            'quote_number' => 'QT-001',
            'invoice_group_id' => 1,
        ]);
        
        config(['settings.generate_quote_number_for_draft' => 0]);

        // Act: Try to generate number
        $this->service->generateQuoteNumberIfApplicable($quote->quote_id);

        // Assert: Quote number should remain unchanged
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id' => $quote->quote_id,
            'quote_number' => 'QT-001',
        ]);
    }

    public function test_generateQuoteNumberIfApplicable_does_not_generate_when_not_draft(): void
    {
        // Arrange: Create sent quote without number
        $quote = Quote::factory()->create([
            'quote_status_id' => 2, // Sent
            'quote_number' => '',
            'invoice_group_id' => 1,
        ]);
        
        config(['settings.generate_quote_number_for_draft' => 0]);

        // Act: Try to generate number
        $this->service->generateQuoteNumberIfApplicable($quote->quote_id);

        // Assert: Quote number should remain empty
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id' => $quote->quote_id,
            'quote_number' => '',
        ]);
    }

    public function test_generateQuoteNumberIfApplicable_does_not_generate_when_setting_enabled(): void
    {
        // Arrange: Create draft quote without number but with setting enabled
        $quote = Quote::factory()->create([
            'quote_status_id' => 1, // Draft
            'quote_number' => '',
            'invoice_group_id' => 1,
        ]);
        
        config(['settings.generate_quote_number_for_draft' => 1]);

        // Act: Try to generate number
        $this->service->generateQuoteNumberIfApplicable($quote->quote_id);

        // Assert: Quote number should remain empty
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id' => $quote->quote_id,
            'quote_number' => '',
        ]);
    }

    public function test_markViewed_handles_nonexistent_quote_gracefully(): void
    {
        // Act: Try to mark non-existent quote as viewed
        $this->service->markViewed(99999);

        // Assert: Should not throw exception
        $this->assertTrue(true);
    }

    public function test_markSent_handles_nonexistent_quote_gracefully(): void
    {
        // Act: Try to mark non-existent quote as sent
        $this->service->markSent(99999);

        // Assert: Should not throw exception
        $this->assertTrue(true);
    }

    public function test_approveQuoteByKey_only_affects_matching_key(): void
    {
        // Arrange: Create multiple quotes with different keys
        $quote1 = Quote::factory()->create([
            'quote_status_id' => 2,
            'quote_url_key' => 'key-1',
        ]);
        $quote2 = Quote::factory()->create([
            'quote_status_id' => 2,
            'quote_url_key' => 'key-2',
        ]);

        // Act: Approve only quote1
        $this->service->approveQuoteByKey('key-1');

        // Assert: Only quote1 should be approved
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id' => $quote1->quote_id,
            'quote_status_id' => 4, // Approved
        ]);
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id' => $quote2->quote_id,
            'quote_status_id' => 2, // Still sent
        ]);
    }

    public function test_rejectQuoteByKey_only_affects_matching_key(): void
    {
        // Arrange: Create multiple quotes with different keys
        $quote1 = Quote::factory()->create([
            'quote_status_id' => 2,
            'quote_url_key' => 'reject-1',
        ]);
        $quote2 = Quote::factory()->create([
            'quote_status_id' => 2,
            'quote_url_key' => 'reject-2',
        ]);

        // Act: Reject only quote1
        $this->service->rejectQuoteByKey('reject-1');

        // Assert: Only quote1 should be rejected
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id' => $quote1->quote_id,
            'quote_status_id' => 5, // Rejected
        ]);
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id' => $quote2->quote_id,
            'quote_status_id' => 2, // Still sent
        ]);
    }
}