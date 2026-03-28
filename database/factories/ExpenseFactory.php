<?php

namespace Database\Factories;

use App\Models\Expense;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category' => $this->faker->randomElement(['rent', 'utilities', 'salary', 'supplies', 'maintenance', 'marketing', 'other']),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->optional()->sentence(),
            'amount' => $this->faker->randomFloat(2, 100, 50000),
            'date' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'payment_method' => $this->faker->randomElement(['cash', 'mobile_pay', 'card', 'bank_transfer']),
        ];
    }
}
