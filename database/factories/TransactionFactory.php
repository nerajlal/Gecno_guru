<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'merchant_order_id' => 'MUID' . Str::random(10) . time(),
            'phonepe_transaction_id' => null,
            'amount' => $this->faker->numberBetween(100, 10000),
            'status' => 'PENDING',
        ];
    }
}
