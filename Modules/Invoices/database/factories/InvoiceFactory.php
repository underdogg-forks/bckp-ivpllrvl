<?php

namespace Modules\Invoices\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Clients\Models\Client;

class InvoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Invoices\Models\Invoice::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $total = $this->faker->randomFloat(2, 100, 10000);

        return [
            'client_id'            => Client::factory(),
            'invoice_status_id'    => $this->faker->numberBetween(1, 4),
            'invoice_number'       => 'INV-' . $this->faker->unique()->numberBetween(1000, 9999),
            'invoice_date_created' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'invoice_date_due'     => $this->faker->dateTimeBetween('now', '+30 days'),
            'invoice_balance'      => $total,
            'invoice_total'        => $total,
            'total'                => $total,
            'invoice_sign'         => 1,
            'invoice_is_recurring' => false,
            'is_read_only'         => false,
        ];
    }
}
