<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Devise;
use App\Models\DepensesGroupe;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DepensesGroupe>
 */
class DepensesGroupeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DepensesGroupe::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'user_id' => User::factory(),
            'devise_id' => Devise::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Configure the model factory to use an existing user.
     *
     * @param int $userId
     * @return $this
     */
    public function forUser(int $userId): self
    {
        return $this->state(function (array $attributes) use ($userId) {
            return [
                'user_id' => $userId,
            ];
        });
    }

    /**
     * Configure the model factory to use an existing devise.
     *
     * @param int $deviseId
     * @return $this
     */
    public function withDevise(int $deviseId): self
    {
        return $this->state(function (array $attributes) use ($deviseId) {
            return [
                'devise_id' => $deviseId,
            ];
        });
    }
}