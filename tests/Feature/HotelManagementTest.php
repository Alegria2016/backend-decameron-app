<?php

namespace Tests\Feature;

use App\Models\Accommodation;
use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\RoomType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;

class HotelManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear datos de prueba
        $this->standard = RoomType::factory()->create(['name' => 'Estándar']);
        $this->junior = RoomType::factory()->create(['name' => 'Junior']);
        $this->suite = RoomType::factory()->create(['name' => 'Suite']);

        $this->sencilla = Accommodation::factory()->create(['type' => 'Sencilla']);
        $this->doble = Accommodation::factory()->create(['type' => 'Doble']);
        $this->triple = Accommodation::factory()->create(['type' => 'Triple']);
        $this->cuadruple = Accommodation::factory()->create(['type' => 'Cuádruple']);

        // Establecer relaciones válidas
        $this->standard->accommodations()->attach([$this->sencilla->id, $this->doble->id]);
        $this->junior->accommodations()->attach([$this->triple->id, $this->cuadruple->id]);
        $this->suite->accommodations()->attach([$this->sencilla->id, $this->doble->id, $this->triple->id]);

        $this->hotel = Hotel::factory()->create([
            'name' => 'Decameron Cartagena',
            'address' => 'Calle 23 58-25',
            'city' => 'Cartagena',
            'nit' => '12345678-9',
            'total_rooms' => 42
        ]);
    }

    public function test_puede_crear_un_hotel(): void
    {
        $response = $this->postJson('/api/v1/hotels', [
            'name' => 'Hotel Test',
            'address' => 'Calle 123',
            'city' => 'Bogotá',
            'nit' => '98765432-1',
            'total_rooms' => 50
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'name' => 'Hotel Test',
                'total_rooms' => 50
            ]);

        $this->assertDatabaseHas('hotels', ['nit' => '98765432-1']);
    }

    public function test_no_puede_crear_hoteles_con_nit_repetido(): void
    {
        $response = $this->postJson('/api/v1/hotels', [
            'name' => 'Hotel Duplicado',
            'address' => 'Otra dirección',
            'city' => 'Medellín',
            'nit' => '12345678-9', // NIT repetido
            'total_rooms' => 30
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nit']);
    }

    public function test_puede_agregar_habitaciones_a_hotel(): void
    {
        $response = $this->postJson("/api/v1/hotels/{$this->hotel->id}/rooms", [
            'room_type_id' => $this->standard->id,
            'accommodation_id' => $this->sencilla->id,
            'quantity' => 10
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('hotel_rooms', [
            'hotel_id' => $this->hotel->id,
            'quantity' => 10
        ]);
    }

    public function test_no_puede_exceder_total_habitaciones(): void
    {
        // Primero agregamos 40 habitaciones
        HotelRoom::factory()->create([
            'hotel_id' => $this->hotel->id,
            'room_type_id' => $this->standard->id,
            'accommodation_id' => $this->sencilla->id,
            'quantity' => 40
        ]);

        // Intentamos agregar 3 más (superaría el límite de 42)
        $response = $this->postJson("/api/v1/hotels/{$this->hotel->id}/rooms", [
            'room_type_id' => $this->standard->id,
            'accommodation_id' => $this->doble->id,
            'quantity' => 3
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['quantity']);
    }

    public function test_valida_combinaciones_tipo_acomodacion(): void
    {
        // Intento inválido: Estándar con Cuádruple
        $response = $this->postJson("/api/v1/hotels/{$this->hotel->id}/rooms", [
            'room_type_id' => $this->standard->id,
            'accommodation_id' => $this->cuadruple->id,
            'quantity' => 5
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['accommodation_id']);
    }

    public function test_no_permite_combinaciones_repetidas(): void
    {
        // Agregamos una habitación
        HotelRoom::factory()->create([
            'hotel_id' => $this->hotel->id,
            'room_type_id' => $this->junior->id,
            'accommodation_id' => $this->triple->id,
            'quantity' => 5
        ]);

        // Intentamos agregar la misma combinación
        $response = $this->postJson("/api/v1/hotels/{$this->hotel->id}/rooms", [
            'room_type_id' => $this->junior->id,
            'accommodation_id' => $this->triple->id,
            'quantity' => 3
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['room_type_id']);
    }

    
    public function test_puede_listar_hoteles_con_sus_habitaciones(): void
    {
        HotelRoom::factory()->create([
            'hotel_id' => $this->hotel->id,
            'room_type_id' => $this->standard->id,
            'accommodation_id' => $this->sencilla->id,
            'quantity' => 10
        ]);

        $this->getJson('/api/v1/hotels')
            ->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonPath('0.name', 'Decameron Cartagena')
            ->assertJsonPath('0.rooms.0.quantity', 10)
            ->assertJsonPath('0.rooms.0.room_type.name', 'Estándar')
            ->assertJsonPath('0.rooms.0.accommodation.type', 'Sencilla');

        
    }

}