<?php

namespace Database\Factories;

use App\Models\RefNationality;
use Illuminate\Database\Eloquent\Factories\Factory;

class RefNationalityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RefNationality::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('NAT_#####')),
            'title' => $this->faker->unique()->country,
        ];
    }
}
