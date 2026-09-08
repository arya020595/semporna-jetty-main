<?php

namespace Database\Factories;

use App\Models\RefActivity;
use Illuminate\Database\Eloquent\Factories\Factory;

class RefActivityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RefActivity::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('ACT_#####')),
            'title' => $this->faker->unique()->word,
        ];
    }
}
