<?php

namespace Database\Factories;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Users\Models\UserCustom>
 */
class UserCustomFactory extends AbstractFactory
{
    public function definition(): array
    {
        return [
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
