<?php

namespace Database\Factories;

use App\Models\RefDestination;
use Illuminate\Database\Eloquent\Factories\Factory;

class RefDestinationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RefDestination::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('DEST_#####')),
            'title' => $this->faker->unique()->city,
            'type' => RefDestination::TYPE_DESTINATION,
        ];
    }

    /**
     * State: a departure/jetty point instead of a destination.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function departure()
    {
        return $this->state(function () {
            return [
                'type' => RefDestination::TYPE_DEPARTURE,
            ];
        });
    }
}
