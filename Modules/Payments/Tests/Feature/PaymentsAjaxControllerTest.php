<?php

namespace Modules\Payments\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Invoices\Models\Invoice;
use Modules\PaymentMethods\Models\PaymentMethod;
use Modules\Payments\Controllers\AjaxController;
use Modules\Users\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(AjaxController::class)]
class PaymentsAjaxControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Invoice $invoice;
    protected PaymentMethod $paymentMethod;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['user_type' => 1, 'user_active' => 1]);
        $this->invoice = Invoice::factory()->create(['invoice_balance' => 100.00]);
        $this->paymentMethod = PaymentMethod::factory()->create();
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_adds_payment_via_ajax_with_valid_data(): void
    {
        $paymentData = [
            'invoice_id' => $this->invoice->invoice_id,
            'payment_amount' => 50.00,
            'payment_method_id' => $this->paymentMethod->payment_method_id,
            'payment_date' => now()->format('Y-m-d')
        ];

        $response = $this->post(route('payments.ajax.add'), $paymentData);

        $response->assertSuccessful();
        $response->assertJson(['success' => 1]);
        $this->assertArrayHasKey('payment_id', $response->json());
        $this->assertDatabaseHas('ip_payments', [
            'invoice_id' => $this->invoice->invoice_id,
            'payment_amount' => 50.00
        ]);
    }

    #[Test]
    public function it_returns_validation_errors_for_invalid_payment(): void
    {
        $paymentData = [
            'invoice_id' => null,
            'payment_amount' => -50.00, // Invalid amount
        ];

        $response = $this->post(route('payments.ajax.add'), $paymentData);

        $response->assertSuccessful();
        $response->assertJson(['success' => 0]);
        $this->assertArrayHasKey('validation_errors', $response->json());
    }

    #[Test]
    public function it_displays_modal_add_payment_form(): void
    {
        $response = $this->post(route('payments.ajax.modalAddPayment'), [
            'invoice_id' => $this->invoice->invoice_id,
            'invoice_balance' => $this->invoice->invoice_balance,
            'invoice_payment_method' => $this->invoice->payment_method,
            'payment_cf_exist' => 'no'
        ]);

        $response->assertSuccessful();
        $response->assertViewHas('payment_methods');
        $response->assertViewHas('invoice_id', $this->invoice->invoice_id);
        $response->assertViewHas('invoice_balance', $this->invoice->invoice_balance);
    }

    #[Test]
    public function it_sanitizes_invoice_id_in_modal(): void
    {
        $response = $this->post(route('payments.ajax.modalAddPayment'), [
            'invoice_id' => '<script>alert("xss")</script>',
            'invoice_balance' => 100,
            'payment_cf_exist' => 'no'
        ]);

        $response->assertSuccessful();
        $response->assertViewHas('invoice_id', function ($id) {
            return !str_contains($id, '<script>');
        });
    }
}

class PaymentMethodsControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['user_type' => 1, 'user_active' => 1]);
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_displays_payment_methods_index(): void
    {
        $response = $this->get(route('payment_methods.index'));

        $response->assertSuccessful();
        $response->assertViewHas('payment_methods');
        $response->assertSee('Payment Methods');
    }

    #[Test]
    public function it_creates_new_payment_method(): void
    {
        $methodData = [
            'payment_method_name' => 'Test Payment Method'
        ];

        $response = $this->post(route('payment_methods.form'), $methodData);

        $response->assertRedirect(route('payment_methods.index'));
        $this->assertDatabaseHas('ip_payment_methods', [
            'payment_method_name' => 'Test Payment Method'
        ]);
    }

    #[Test]
    public function it_prevents_duplicate_payment_method_names(): void
    {
        PaymentMethod::factory()->create(['payment_method_name' => 'Existing Method']);

        $methodData = [
            'payment_method_name' => 'Existing Method',
            'is_update' => 0
        ];

        $response = $this->post(route('payment_methods.form'), $methodData);

        $response->assertRedirect(route('payment_methods.form'));
        $response->assertSessionHas('alert_error');
    }

    #[Test]
    public function it_updates_existing_payment_method(): void
    {
        $method = PaymentMethod::factory()->create(['payment_method_name' => 'Original']);

        $updateData = [
            'payment_method_name' => 'Edited Payment Method'
        ];

        $response = $this->post(route('payment_methods.form', ['id' => $method->payment_method_id]), $updateData);

        $response->assertRedirect(route('payment_methods.index'));
        $this->assertDatabaseHas('ip_payment_methods', [
            'payment_method_id' => $method->payment_method_id,
            'payment_method_name' => 'Edited Payment Method'
        ]);
    }

    #[Test]
    public function it_deletes_payment_method(): void
    {
        $method = PaymentMethod::factory()->create();

        $response = $this->delete(route('payment_methods.delete', ['id' => $method->payment_method_id]));

        $response->assertRedirect(route('payment_methods.index'));
        $this->assertDatabaseMissing('ip_payment_methods', ['payment_method_id' => $method->payment_method_id]);
    }

    #[Test]
    public function it_cancels_payment_method_form_and_redirects(): void
    {
        $response = $this->post(route('payment_methods.form'), ['btn_cancel' => true]);

        $response->assertRedirect(route('payment_methods.index'));
    }
}
