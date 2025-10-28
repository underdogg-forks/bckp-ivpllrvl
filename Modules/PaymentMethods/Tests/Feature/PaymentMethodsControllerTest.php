<?php

namespace Modules\PaymentMethods\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\PaymentMethods\Controllers\PaymentMethodsController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

use function Tests\Feature\PaymentMethods\route;

use Tests\TestCase;

#[CoversClass(PaymentMethodsController::class)]
class PaymentMethodsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_payment_methods_list()
    {
        // Arrange: create payment methods
        $paymentMethod = \Modules\PaymentMethods\Models\PaymentMethod::factory()->create();

        // Act: visit payment methods index
        $response = $this->get(route('payment_methods.index'));

        // Assert: payment methods are displayed
        $response->assertStatus(200);
        $response->assertSee($paymentMethod->payment_method_name);
    }

    #[Test]
    public function it_displays_payment_method_form_for_new_method()
    {
        // Act: visit new payment method form
        $response = $this->get(route('payment_methods.form'));

        // Assert: form is displayed
        $response->assertStatus(200);
    }

    #[Test]
    public function it_redirects_when_cancel_button_is_clicked()
    {
        // Act: submit form with cancel button
        $response = $this->post(route('payment_methods.form'), [
            'btn_cancel' => true,
        ]);

        // Assert: redirects to payment methods index
        $response->assertRedirect(route('payment_methods'));
    }

    #[Test]
    public function it_deletes_payment_method()
    {
        // Arrange: create a payment method
        $paymentMethod = \Modules\PaymentMethods\Models\PaymentMethod::factory()->create();

        // Act: delete the payment method
        $response = $this->get(route('payment_methods.delete', ['id' => $paymentMethod->id]));

        // Assert: redirects and payment method is deleted
        $response->assertRedirect(route('payment_methods'));
        $this->assertDatabaseMissing('ip_payment_methods', ['payment_method_id' => $paymentMethod->id]);
    }
}
