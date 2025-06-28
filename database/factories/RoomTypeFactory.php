<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoomTypeFactory extends Factory
{
    protected $model = \App\Models\RoomType::class;

    public function definition(): array
    {
        $types = ['Estándar', 'Junior', 'Suite'];
        
        return [
            'name' => $this->faker->unique()->randomElement($types),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function standard(): static
    {
        return $this->state(['name' => 'Estándar']);
    }

    public function junior(): static
    {
        return $this->state(['name' => 'Junior']);
    }

    public function suite(): static
    {
        return $this->state(['name' => 'Suite']);
    }
}