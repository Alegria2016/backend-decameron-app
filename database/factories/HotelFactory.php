<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class HotelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'nit' => $this->faker->unique()->numerify('########-#'),
            'total_rooms' => $this->faker->numberBetween(10, 100),
            'image' => $this->faker->imageUrl(640, 480, 'hotel', true, 'Faker'), // Generates a random hotel image URL
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}