<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'theme' => fake()->sentence(),
            'text' => fake()->paragraph(),
            'status' => fake()->randomElement(['new', 'in_progress', 'done']),
            'date_answer' => fake()->optional()->dateTime(),
        ];
    }
}
