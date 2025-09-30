<?php

namespace Tests\Unit\Views\QuoteTemplates\Public;

use Tests\TestCase;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Support\Facades\Session;

/**
 * Unit tests for InvoicePlane Web Quote View Template.
 *
 * Testing Library & Framework: PHPUnit 11.x with Laravel 12 TestCase + InteractsWithViews trait.
 */
class InvoicePlaneWebViewTest extends TestCase
{
    use InteractsWithViews;

    /**
     * Build a representative quote object for the Blade view.
     */
    private function makeQuote(array $overrides = []): object
    {
        return (object) array_merge([
            'quote_number' => 'QT-2024-001',
            'quote_status_id' => 2,
            'quote_date_created' => '2024-01-15',
            'quote_date_expires' => '2024-02-15',
            'quote_total' => 1500.00,
            'quote_item_subtotal' => 1300.00,
            'quote_item_tax_total' => 200.00,
            'quote_discount_percent' => 0.0,
            'quote_discount_amount' => 0.0,
            'user_vat_id' => 'VAT123456',
            'user_tax_code' => 'TAX789',
            'user_address_1' => '123 Business St',
            'user_address_2' => 'Suite 100',
            'user_city' => 'New York',
            'user_state' => 'NY',
            'user_zip' => '10001',
            'user_phone' => '555-0100',
            'user_fax' => '555-0101',
            'client_name' => 'Test Client Ltd',
            'client_vat_id' => 'VAT654321',
            'client_tax_code' => 'TAX987',
            'client_address_1' => '456 Client Ave',
            'client_address_2' => 'Floor 2',
            'client_city' => 'Boston',
            'client_state' => 'MA',
            'client_zip' => '02101',
            'client_phone' => '555-0200',
            'notes' => 'Thank you for your business.',
        ], $overrides);
    }

    /**
     * Build a representative list of quote items.
     */
    private function makeItems(): array
    {
        return [
            (object) [
                'item_name' => 'Web Development',
                'item_description' => 'Custom website development',
                'item_quantity' => 1,
                'item_price' => 1000.00,
                'item_discount' => 0.00,
                'item_subtotal' => 1000.00,
                'item_product_unit' => 'hour',
            ],
            (object) [
                'item_name' => 'Design Services',
                'item_description' => 'UI/UX design work',
                'item_quantity' => 2,
                'item_price' => 150.00,
                'item_discount' => 0.00,
                'item_subtotal' => 300.00,
                'item_product_unit' => 'hour',
            ],
        ];
    }

    /**
     * Build sample tax rates used in summaries.
     */
    private function makeTaxRates(): array
    {
        return [
            (object) [
                'quote_tax_rate_name' => 'Sales Tax',
                'quote_tax_rate_percent' => 10.0,
                'quote_tax_rate_amount' => 130.00,
            ],
        ];
    }

    private function renderView(array $params)
    {
        return $this->view('quote_templates.public.InvoicePlane_Web', $params);
    }

    public function test_view_renders_with_complete_dataset(): void
    {
        $view = $this->renderView([
            'quote' => $this->makeQuote(),
            'items' => $this->makeItems(),
            'quote_tax_rates' => $this->makeTaxRates(),
            'quote_url_key' => 'key-123',
            'flash_message' => null,
            'is_expired' => false,
            'legacy_calculation' => false,
            'attachments' => [],
        ]);

        $view->assertSee('QT-2024-001');
        $view->assertSee('Test Client Ltd');
        $view->assertSee('Web Development');
    }

    public function test_quote_number_visible_in_title_and_heading(): void
    {
        $view = $this->renderView([
            'quote' => $this->makeQuote(['quote_number' => 'QUOTE-ABC']),
            'items' => [],
            'quote_tax_rates' => [],
            'quote_url_key' => 'key',
            'flash_message' => null,
            'is_expired' => false,
            'legacy_calculation' => false,
            'attachments' => [],
        ]);

        $view->assertSee('QUOTE-ABC');
        $view->assertSeeInOrder(['quote', 'QUOTE-ABC']);
    }

    public function test_action_buttons_visible_when_status_sent(): void
    {
        $view = $this->renderView([
            'quote' => $this->makeQuote(['quote_status_id' => 2]),
            'items' => [],
            'quote_tax_rates' => [],
            'quote_url_key' => 'approve-me',
            'flash_message' => null,
            'is_expired' => false,
            'legacy_calculation' => false,
            'attachments' => [],
        ]);

        $view->assertSee('approve_this_quote');
        $view->assertSee('reject_this_quote');
        $view->assertSee('guest/view/approve_quote/approve-me', false);
        $view->assertSee('guest/view/reject_quote/approve-me', false);
    }

    public function test_action_buttons_visible_when_status_viewed(): void
    {
        $view = $this->renderView([
            'quote' => $this->makeQuote(['quote_status_id' => 3]),
            'items' => [],
            'quote_tax_rates' => [],
            'quote_url_key' => 'approve-me',
            'flash_message' => null,
            'is_expired' => false,
            'legacy_calculation' => false,
            'attachments' => [],
        ]);

        $view->assertSee('approve_this_quote');
        $view->assertSee('reject_this_quote');
    }

    public function test_action_buttons_hidden_for_unsent_status(): void
    {
        $view = $this->renderView([
            'quote' => $this->makeQuote(['quote_status_id' => 1]),
            'items' => [],
            'quote_tax_rates' => [],
            'quote_url_key' => 'key',
            'flash_message' => null,
            'is_expired' => false,
            'legacy_calculation' => false,
            'attachments' => [],
        ]);

        $view->assertDontSee('approve_this_quote');
        $view->assertDontSee('reject_this_quote');
    }

    public function test_pdf_download_button_always_rendered(): void
    {
        $view = $this->renderView([
            'quote' => $this->makeQuote(),
            'items' => [],
            'quote_tax_rates' => [],
            'quote_url_key' => 'pdf-key',
            'flash_message' => null,
            'is_expired' => false,
            'legacy_calculation' => false,
            'attachments' => [],
        ]);

        $view->assertSee('download_pdf');
        $view->assertSee('guest/view/generate_quote_pdf/pdf-key', false);
    }

    public function test_dashboard_button_requires_session_authentication(): void
    {
        Session::put('user_id', 5);
        Session::put('user_type', 1);

        $view = $this->renderView([
            'quote' => $this->makeQuote(),
            'items' => [],
            'quote_tax_rates' => [],
            'quote_url_key' => 'key',
            'flash_message' => null,
            'is_expired' => false,
            'legacy_calculation' => false,
            'attachments' => [],
        ]);

        $view->assertSee('dashboard');

        Session::forget(['user_id', 'user_type']);
    }

    public function test_flash_message_block(): void
    {
        $view = $this->renderView([
            'quote' => $this->makeQuote(),
            'items' => [],
            'quote_tax_rates' => [],
            'quote_url_key' => 'key',
            'flash_message' => 'Quote updated\!',
            'is_expired' => false,
            'legacy_calculation' => false,
            'attachments' => [],
        ]);

        $view->assertSee('Quote updated\!');
        $view->assertSee('alert alert-info', false);
    }

    public function test_client_information_presence(): void
    {
        $view = $this->renderView([
            'quote' => $this->makeQuote([
                'client_name' => 'Acme Inc',
                'client_vat_id' => 'VAT-99',
                'client_address_1' => '789 Client Street',
                'client_city' => 'Chicago',
                'client_state' => 'IL',
                'client_zip' => '60601',
                'client_phone' => '555-9999',
            ]),
            'items' => [],
            'quote_tax_rates' => [],
            'quote_url_key' => 'key',
            'flash_message' => null,
            'is_expired' => false,
            'legacy_calculation' => false,
            'attachments' => [],
        ]);

        $view->assertSee('Acme Inc');
        $view->assertSee('VAT-99');
        $view->assertSee('789 Client Street');
        $view->assertSee('Chicago');
        $view->assertSee('60601');
        $view->assertSee('555-9999');
    }

    public function test_company_information_presence(): void
    {
        $view = $this->renderView([
            'quote' => $this->makeQuote([
                'user_vat_id' => 'VAT-11',
                'user_tax_code' => 'TAX-33',
                'user_address_1' => '123 Company Rd',
                'user_city' => 'Los Angeles',
                'user_state' => 'CA',
                'user_zip' => '90001',
                'user_phone' => '555-1111',
            ]),
            'items' => [],
            'quote_tax_rates' => [],
            'quote_url_key' => 'key',
            'flash_message' => null,
            'is_expired' => false,
            'legacy_calculation' => false,
            'attachments' => [],
        ]);

        $view->assertSee('VAT-11');
        $view->assertSee('TAX-33');
        $view->assertSee('123 Company Rd');
        $view->assertSee('Los Angeles');
        $view->assertSee('90001');
        $view->assertSee('555-1111');
    }

    public function test_items_rendered_in_table_rows(): void
    {
        $view = $this->renderView([
            'quote' => $this->makeQuote(),
            'items' => [
                (object) [
                    'item_name' => 'Consulting',
                    'item_description' => 'Technical consulting',
                    'item_quantity' => 10,
                    'item_price' => 100.00,
                    'item_discount' => 50.00,
                    'item_subtotal' => 950.00,
                    'item_product_unit' => 'hours',
                ],
            ],
            'quote_tax_rates' => [],
            'quote_url_key' => 'key',
            'flash_message' => null,
            'is_expired' => false,
            'legacy_calculation' => false,
            'attachments' => [],
        ]);

        $view->assertSee('Consulting');
        $view->assertSee('Technical consulting');
        $view->assertSee('hours');
    }

    public function test_item_quantity_and_unit_display(): void
    {
        $view = $this->renderView([
            'quote' => $this->makeQuote(),
            'items' => [
                (object) [
                    'item_name' => 'Product A',
                    'item_description' => 'Description A',
                    'item_quantity' => 5,
                    'item_price' => 20.00,
                    'item_discount' => 0.00,
                    'item_subtotal' => 100.00,
                    'item_product_unit' => 'pieces',
                ],
            ],
            'quote_tax_rates' => [],
            'quote_url_key' => 'key',
            'flash_message' => null,
            'is_expired' => false,
            'legacy_calculation' => false,
            'attachments' => [],
        ]);

        $view->assertSee('Product A');
        $view->assertSee('pieces');
    }

    public function test_discount_section_for_non_legacy_calculation(): void
    {
        $view = $this->renderView([
            'quote' => $this->makeQuote([
                'quote_discount_percent' => 10.0,
                'quote_discount_amount' => 0.0,
            ]),
            'items' => [],
            'quote_tax_rates' => [],
            'quote_url_key' => 'key',
            'flash_message' => null,
            'is_expired' => false,
            'legacy_calculation' => false,
            'attachments' => [],
        ]);

        $view->assertSee('discount');
    }

    public function test_discount_section_for_legacy_calculation(): void
    {
        $view = $this->renderView([
            'quote' => $this->makeQuote([
                'quote_discount_percent' => 5.0,
                'quote_discount_amount' => 0.0,
            ]),
            'items' => [],
            'quote_tax_rates' => [],
            'quote_url_key' => 'key',
            'flash_message' => null,
            'is_expired' => false,
            'legacy_calculation' => true,
            'attachments' => [],
        ]);

        $view->assertSee('discount');
    }

    public function test_percentage_discount_value_displayed(): void
    {
        $view = $this->renderView([
            'quote' => $this->makeQuote([
                'quote_discount_percent' => 15.0,
                'quote_discount_amount' => 0.0,
            ]),
            'items' => [],
            'quote_tax_rates' => [],
            'quote_url_key' => 'key',
            'flash_message' => null,
            'is_expired' => false,
            'legacy_calculation' => false,
            'attachments' => [],
        ]);

        $view->assertSee('discount');
    }

    public function test_tax_rates_summary_rows(): void
    {
        $view = $this->renderView([
            'quote' => $this->makeQuote(),
            'items' => [],
            'quote_tax_rates' => [
                (object) [
                    'quote_tax_rate_name' => 'VAT',
                    'quote_tax_rate_percent' => 20.00,
                    'quote_tax_rate_amount' => 260.00,
                ],
                (object) [
                    'quote_tax_rate_name' => 'Local Tax',
                    'quote_tax_rate_percent' => 5.00,
                    'quote_tax_rate_amount' => 65.00,
                ],
            ],
            'quote_url_key' => 'key',
            'flash_message' => null,
            'is_expired' => false,
            'legacy_calculation' => false,
            'attachments' => [],
        ]);

        $view->assertSee('VAT');
        $view->assertSee('Local Tax');
    }

    public function test_item_tax_total_visible_when_positive(): void
    {
        $view = $this->renderView([
            'quote' => $this->makeQuote(['quote_item_tax_total' => 100.00]),
            'items' => [],
            'quote_tax_rates' => [],
            'quote_url_key' => 'key',
            'flash_message' => null,
            'is_expired' => false,
            'legacy_calculation' => false,
            'attachments' => [],
        ]);

        $view->assertSee('item_tax');
    }

    public function test_notes_section_when_notes_present(): void
    {
        $view = $this->renderView([
            'quote' => $this->makeQuote(['notes' => 'Please remit within 30 days.']),
            'items' => [],
            'quote_tax_rates' => [],
            'quote_url_key' => 'key',
            'flash_message' => null,
            'is_expired' => false,
            'legacy_calculation' => false,
            'attachments' => [],
        ]);

        $view->assertSee('Please remit within 30 days.');
    }

    public function test_attachments_section_with_files(): void
    {
        $view = $this->renderView([
            'quote' => $this->makeQuote(),
            'items' => [],
            'quote_tax_rates' => [],
            'quote_url_key' => 'key',
            'flash_message' => null,
            'is_expired' => false,
            'legacy_calculation' => false,
            'attachments' => [
                ['name' => 'contract.pdf', 'fullname' => 'uploads/contract_123.pdf'],
                ['name' => 'specifications.docx', 'fullname' => 'uploads/specs_456.docx'],
            ],
        ]);

        $view->assertSee('attachments');
        $view->assertSee('contract.pdf');
        $view->assertSee('specifications.docx');
        $view->assertSee('guest/get/attachment/uploads/contract_123.pdf', false);
    }

    public function test_expired_flag_adds_overdue_class(): void
    {
        $view = $this->renderView([
            'quote' => $this->makeQuote(['quote_date_expires' => '2023-01-01']),
            'items' => [],
            'quote_tax_rates' => [],
            'quote_url_key' => 'key',
            'flash_message' => null,
            'is_expired' => true,
            'legacy_calculation' => false,
            'attachments' => [],
        ]);

        $view->assertSee('overdue', false);
    }

    public function test_dates_section_presence(): void
    {
        $view = $this->renderView([
            'quote' => $this->makeQuote([
                'quote_date_created' => '2024-01-01',
                'quote_date_expires' => '2024-02-01',
            ]),
            'items' => [],
            'quote_tax_rates' => [],
            'quote_url_key' => 'key',
            'flash_message' => null,
            'is_expired' => false,
            'legacy_calculation' => false,
            'attachments' => [],
        ]);

        $view->assertSee('quote_date');
        $view->assertSee('expires');
    }

    public function test_subtotal_and_total_summary_visibility(): void
    {
        $view = $this->renderView([
            'quote' => $this->makeQuote([
                'quote_item_subtotal' => 1000.00,
                'quote_total' => 1200.00,
            ]),
            'items' => [],
            'quote_tax_rates' => [],
            'quote_url_key' => 'key',
            'flash_message' => null,
            'is_expired' => false,
            'legacy_calculation' => false,
            'attachments' => [],
        ]);

        $view->assertSee('subtotal');
        $view->assertSee('total');
    }

    public function test_empty_items_collection_renders_without_errors(): void
    {
        $view = $this->renderView([
            'quote' => $this->makeQuote(),
            'items' => [],
            'quote_tax_rates' => [],
            'quote_url_key' => 'key',
            'flash_message' => null,
            'is_expired' => false,
            'legacy_calculation' => false,
            'attachments' => [],
        ]);

        $view->assertSee('quote');
        $view->assertDontSee('undefined');
    }

    public function test_item_description_escapes_html(): void
    {
        $view = $this->renderView([
            'quote' => $this->makeQuote(),
            'items' => [
                (object) [
                    'item_name' => 'Test Item',
                    'item_description' => '<script>alert("xss")</script>',
                    'item_quantity' => 1,
                    'item_price' => 100.00,
                    'item_discount' => 0.00,
                    'item_subtotal' => 100.00,
                    'item_product_unit' => null,
                ],
            ],
            'quote_tax_rates' => [],
            'quote_url_key' => 'key',
            'flash_message' => null,
            'is_expired' => false,
            'legacy_calculation' => false,
            'attachments' => [],
        ]);

        $view->assertDontSee('<script>alert("xss")</script>', false);
        $view->assertSee('Test Item');
    }

    public function test_optional_fields_accept_nulls(): void
    {
        $view = $this->renderView([
            'quote' => $this->makeQuote([
                'user_address_2' => null,
                'user_fax' => null,
                'client_address_2' => null,
                'notes' => null,
            ]),
            'items' => [],
            'quote_tax_rates' => [],
            'quote_url_key' => 'key',
            'flash_message' => null,
            'is_expired' => false,
            'legacy_calculation' => false,
            'attachments' => [],
        ]);

        $view->assertSee('quote');
    }

    public function test_multiple_items_preserve_order(): void
    {
        $view = $this->renderView([
            'quote' => $this->makeQuote(),
            'items' => [
                (object) [
                    'item_name' => 'First Item',
                    'item_description' => 'First description',
                    'item_quantity' => 1,
                    'item_price' => 100.00,
                    'item_discount' => 0.00,
                    'item_subtotal' => 100.00,
                    'item_product_unit' => null,
                ],
                (object) [
                    'item_name' => 'Second Item',
                    'item_description' => 'Second description',
                    'item_quantity' => 2,
                    'item_price' => 50.00,
                    'item_discount' => 0.00,
                    'item_subtotal' => 100.00,
                    'item_product_unit' => null,
                ],
            ],
            'quote_tax_rates' => [],
            'quote_url_key' => 'key',
            'flash_message' => null,
            'is_expired' => false,
            'legacy_calculation' => false,
            'attachments' => [],
        ]);

        $view->assertSeeInOrder(['First Item', 'Second Item']);
    }

    public function test_item_table_headers_present(): void
    {
        $view = $this->renderView([
            'quote' => $this->makeQuote(),
            'items' => [],
            'quote_tax_rates' => [],
            'quote_url_key' => 'key',
            'flash_message' => null,
            'is_expired' => false,
            'legacy_calculation' => false,
            'attachments' => [],
        ]);

        foreach (['item', 'description', 'qty', 'price', 'discount'] as $header) {
            $view->assertSee($header);
        }
    }

    public function test_amount_discount_displayed_when_percent_zero(): void
    {
        $view = $this->renderView([
            'quote' => $this->makeQuote([
                'quote_discount_percent' => 0.0,
                'quote_discount_amount' => 50.00,
            ]),
            'items' => [],
            'quote_tax_rates' => [],
            'quote_url_key' => 'key',
            'flash_message' => null,
            'is_expired' => false,
            'legacy_calculation' => false,
            'attachments' => [],
        ]);

        $view->assertSee('discount');
    }

    public function test_basic_html_structure_present(): void
    {
        $content = (string) $this->renderView([
            'quote' => $this->makeQuote(),
            'items' => [],
            'quote_tax_rates' => [],
            'quote_url_key' => 'key',
            'flash_message' => null,
            'is_expired' => false,
            'legacy_calculation' => false,
            'attachments' => [],
        ]);

        $this->assertStringContainsString('<\!DOCTYPE html>', $content);
        $this->assertStringContainsString('<html', $content);
        $this->assertStringContainsString('</html>', $content);
        $this->assertStringContainsString('<body>', $content);
        $this->assertStringContainsString('</body>', $content);
    }

    public function test_meta_viewport_for_responsiveness(): void
    {
        $view = $this->renderView([
            'quote' => $this->makeQuote(),
            'items' => [],
            'quote_tax_rates' => [],
            'quote_url_key' => 'key',
            'flash_message' => null,
            'is_expired' => false,
            'legacy_calculation' => false,
            'attachments' => [],
        ]);

        $view->assertSee('viewport', false);
        $view->assertSee('width=device-width', false);
    }

    public function test_stylesheets_are_linked(): void
    {
        $view = $this->renderView([
            'quote' => $this->makeQuote(),
            'items' => [],
            'quote_tax_rates' => [],
            'quote_url_key' => 'key',
            'flash_message' => null,
            'is_expired' => false,
            'legacy_calculation' => false,
            'attachments' => [],
        ]);

        $view->assertSee('stylesheet', false);
        $view->assertSee('.css', false);
    }
}