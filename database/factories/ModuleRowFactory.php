<?php

namespace Database\Factories;

use App\Models\Module;
use App\Models\Account;
use App\Models\ModuleRow;
use Illuminate\Database\Eloquent\Factories\Factory;

class ModuleRowFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ModuleRow::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'module_id' => Module::factory(),
            'position' => 1,
        ];
    }
}
