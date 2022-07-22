<?php

namespace Database\Factories;

use App\Models\Gift;
use App\Models\GiftUrl;
use Illuminate\Database\Eloquent\Factories\Factory;

class GiftUrlFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GiftUrl::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'gift_id' => Gift::factory(),
            'url' => $this->faker->url(),
        ];
    }
}
