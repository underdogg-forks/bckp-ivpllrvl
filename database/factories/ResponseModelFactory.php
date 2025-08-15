<?php

namespace Database\Factories;

use Database\Factories\AbstractSeeder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ResponseModel>
 */
class ResponseModelFactory extends AbstractFactory
{
    public function definition(): array
    {
        return [
            //
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Franchise $franchise) {
            // Create related models here
            // Example:
            // RelatedModel::factory()->count(3)->create(['company_id' => $franchise->id]);
        });
    }
}
