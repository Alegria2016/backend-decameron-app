<?php

namespace Database\Factories;

use App\Models\Accommodation;
use App\Models\Hotel;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

class HotelRoomFactory extends Factory
{
    protected $model = \App\Models\HotelRoom::class;

    public function definition(): array
    {
        return [
            'hotel_id' => Hotel::factory(),
            'room_type_id' => RoomType::factory(),
            'accommodation_id' => Accommodation::factory(),
            'quantity' => $this->faker->numberBetween(1, 10),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function forStandardRoom(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'room_type_id' => RoomType::factory()->standard(),
                'accommodation_id' => Accommodation::factory()->state(function () {
                    return ['type' => $this->faker->randomElement(['Sencilla', 'Doble'])];
                }),
            ];
        });
    }

    public function forJuniorRoom(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'room_type_id' => RoomType::factory()->junior(),
                'accommodation_id' => Accommodation::factory()->state(function () {
                    return ['type' => $this->faker->randomElement(['Triple', 'Cuádruple'])];
                }),
            ];
        });
    }

    public function forSuiteRoom(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'room_type_id' => RoomType::factory()->suite(),
                'accommodation_id' => Accommodation::factory()->state(function () {
                    return ['type' => $this->faker->randomElement(['Sencilla', 'Doble', 'Triple'])];
                }),
            ];
        });
    }
}