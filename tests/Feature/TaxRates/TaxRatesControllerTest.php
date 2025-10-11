<?php

namespace Tests\Feature\TaxRates;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\TaxRates\Controllers\TaxRatesController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(TaxRatesController::class)]
class TaxRatesControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_tax_rates_list()
    {
        // Arrange: create tax rates
        $taxRate = \Modules\TaxRates\Models\TaxRate::factory()->create();

        // Act: visit tax rates index
        $response = $this->get(route('tax_rates.index'));

        // Assert: tax rates are displayed
        $response->assertStatus(200);
        $response->assertViewIs('tax_rates.index');
        $response->assertSee($taxRate->tax_rate_name);
    }

    #[Test]
    public function it_displays_tax_rate_form_for_new_rate()
    {
        // Act: visit new tax rate form
        $response = $this->get(route('tax_rates.form'));

        // Assert: form is displayed
        $response->assertStatus(200);
        $response->assertViewIs('tax_rates.form');
    }

    #[Test]
    public function it_displays_tax_rate_form_for_existing_rate()
    {
        // Arrange: create a tax rate
        $taxRate = \Modules\TaxRates\Models\TaxRate::factory()->create();

        // Act: visit tax rate edit form
        $response = $this->get(route('tax_rates.form', ['id' => $taxRate->id]));

        // Assert: form is displayed
        $response->assertStatus(200);
        $response->assertViewIs('tax_rates.form');
    }

    #[Test]
    public function it_redirects_when_cancel_button_is_clicked()
    {
        // Act: submit form with cancel button
        $response = $this->post(route('tax_rates.form'), [
            'btn_cancel' => true,
        ]);

        // Assert: redirects to tax rates index
        $response->assertRedirect(route('tax_rates.index'));
    }

    #[Test]
    public function it_deletes_tax_rate()
    {
        // Arrange: create a tax rate
        $taxRate = \Modules\TaxRates\Models\TaxRate::factory()->create();

        // Act: delete the tax rate
        $response = $this->get(route('tax_rates.delete', ['id' => $taxRate->id]));

        // Assert: redirects and tax rate is deleted
        $response->assertRedirect(route('tax_rates.index'));
        $this->assertDatabaseMissing('ip_tax_rates', ['tax_rate_id' => $taxRate->id]);
    }

    #[Test]
    public function it_stores_tax_rate_via_form_store()
    {
        // Act: submit form with valid data
        /**
         * Payload:
         * {
         *   "tax_rate_name": "VAT",
         *   "tax_rate_percent": "20.00",
         *   "btn_submit": true
         * }
         */
        $response = $this->post(route('tax_rates.formStore'), [
            'tax_rate_name' => 'VAT',
            'tax_rate_percent' => '20.00',
            'btn_submit' => true,
        ]);

        // Assert: redirects to tax rates index
        $response->assertRedirect(route('tax_rates.index'));
    }
}
