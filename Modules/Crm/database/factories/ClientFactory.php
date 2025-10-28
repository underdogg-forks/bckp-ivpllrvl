<?php

namespace Modules\Clients\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Clients\Models\tmpClient::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'client_name'    => $this->faker->company(),
            'client_email'   => $this->faker->companyEmail(),
            'client_phone'   => $this->faker->phoneNumber(),
            'client_address' => $this->faker->streetAddress(),
            'client_city'    => $this->faker->city(),
            'client_state'   => $this->faker->state(),
            'client_zip'     => $this->faker->postcode(),
            'client_country' => $this->faker->country(),
        ];
    }
}
