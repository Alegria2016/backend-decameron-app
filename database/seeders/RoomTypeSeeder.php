<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    // Crear tipos de habitación
    $standard = RoomType::factory()->standard()->create();
    $junior = RoomType::factory()->junior()->create();
    $suite = RoomType::factory()->suite()->create();

    // Crear acomodaciones
    $sencilla = Accommodation::factory()->sencilla()->create();
    $doble = Accommodation::factory()->doble()->create();
    $triple = Accommodation::factory()->triple()->create();
    $cuadruple = Accommodation::factory()->cuadruple()->create();

    // Establecer relaciones válidas
    $standard->accommodations()->attach([$sencilla->id, $doble->id]);
    $junior->accommodations()->attach([$triple->id, $cuadruple->id]);
    $suite->accommodations()->attach([$sencilla->id, $doble->id, $triple->id]);

    // Crear hoteles con habitaciones
    Hotel::factory()
        ->count(10)
        ->has(HotelRoom::factory()->count(3))
        ->create();
}
}
