<?php

namespace Database\Factories;

use App\Models\Wastage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Wastage>
 */
class WastageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'quantity' => $this->faker->randomFloat(3, 0.1, 50),
            'unit' => $this->faker->randomElement(['kg', 'g', 'l', 'ml', 'pcs']),
            'reason' => $this->faker->randomElement(['expired', 'damaged', 'spillage', 'preparation_error', 'quality_issue', 'other']),
            'date' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'estimated_cost' => $this->faker->randomFloat(2, 10, 5000),
        ];
    }
}
