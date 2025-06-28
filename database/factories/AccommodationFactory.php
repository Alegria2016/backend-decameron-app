<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AccommodationFactory extends Factory
{
    protected $model = \App\Models\Accommodation::class;

    public function definition(): array
    {
        $types = ['Sencilla', 'Doble', 'Triple', 'Cuádruple'];
        
        return [
            'type' => $this->faker->unique()->randomElement($types),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function sencilla(): static
    {
        return $this->state(['type' => 'Sencilla']);
    }

    public function doble(): static
    {
        return $this->state(['type' => 'Doble']);
    }

    public function triple(): static
    {
        return $this->state(['type' => 'Triple']);
    }

    public function cuadruple(): static
    {
        return $this->state(['type' => 'Cuádruple']);
    }
}