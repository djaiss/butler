<?php

namespace Database\Factories;

use App\Models\Gift;
use App\Models\GiftOccasion;
use App\Models\GiftState;
use App\Models\Vault;
use Illuminate\Database\Eloquent\Factories\Factory;

class GiftFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Gift::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'vault_id' => Vault::factory(),
            'gift_occasion_id' => GiftOccasion::factory(),
            'gift_state_id' => GiftState::factory(),
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'budget' => $this->faker->randomNumber(),
            'received_at' => $this->faker->dateTimeThisCentury(),
            'given_at' => $this->faker->dateTimeThisCentury(),
            'bought_at' => $this->faker->dateTimeThisCentury(),
        ];
    }
}
