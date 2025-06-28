<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RoomType;
use App\Models\Accommodation;

class RoomTypeAccommodationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Tipos de habitación
        $standard = RoomType::create(['name' => 'Estándar']);
        $junior = RoomType::create(['name' => 'Junior']);
        $suite = RoomType::create(['name' => 'Suite']);

        // Acomodaciones
        $sencilla = Accommodation::create(['type' => 'Sencilla']);
        $doble = Accommodation::create(['type' => 'Doble']);
        $triple = Accommodation::create(['type' => 'Triple']);
        $cuadruple = Accommodation::create(['type' => 'Cuádruple']);

        // Relaciones válidas
        $standard->accommodations()->attach([$sencilla->id, $doble->id]);
        $junior->accommodations()->attach([$triple->id, $cuadruple->id]);
        $suite->accommodations()->attach([$sencilla->id, $doble->id, $triple->id]);
    }
}
