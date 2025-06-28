<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\RoomType;
use App\Models\Accommodation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class HotelRoomController extends Controller
{
    public function index(Hotel $hotel)
    {
        return response()->json($hotel->rooms()->with('roomType', 'accommodation')->get());
    }

    public function store(Request $request, Hotel $hotel)
    {
        $validator = Validator::make($request->all(), [
            'room_type_id' => 'required|exists:room_types,id',
            'accommodation_id' => 'required|exists:accommodations,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validated = $validator->validated();

        // Validar combinación tipo-acomodación
        $roomType = RoomType::find($validated['room_type_id']);
        $accommodation = Accommodation::find($validated['accommodation_id']);

        if (!$roomType->accommodations->contains($accommodation)) {
            throw ValidationException::withMessages([
                'accommodation_id' => 'La acomodación no es válida para este tipo de habitación'
            ]);
        }

        // Validar duplicados
        if ($hotel->rooms()
            ->where('room_type_id', $validated['room_type_id'])
            ->where('accommodation_id', $validated['accommodation_id'])
            ->exists()) {
            throw ValidationException::withMessages([
                'room_type_id' => 'Esta combinación de tipo y acomodación ya existe para este hotel'
            ]);
        }

        // Validar total de habitaciones
        $currentTotal = $hotel->rooms()->sum('quantity');
        $newTotal = $currentTotal + $validated['quantity'];

        if ($newTotal > $hotel->total_rooms) {
            throw ValidationException::withMessages([
                'quantity' => 'La cantidad excede el total de habitaciones del hotel'
            ]);
        }

        $hotelRoom = $hotel->rooms()->create($validated);

        return response()->json($hotelRoom->load('roomType', 'accommodation'), 201);
    }

    public function show(Hotel $hotel, HotelRoom $room)
    {
        if ($room->hotel_id !== $hotel->id) {
            abort(404);
        }

        return response()->json($room->load('roomType', 'accommodation'));
    }

    public function update(Request $request, Hotel $hotel, HotelRoom $room)
    {
        if ($room->hotel_id !== $hotel->id) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'room_type_id' => 'sometimes|required|exists:room_types,id',
            'accommodation_id' => 'sometimes|required|exists:accommodations,id',
            'quantity' => 'sometimes|required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validated = $validator->validated();

        // Validar combinación tipo-acomodación si se cambia
        if (isset($validated['room_type_id']) || isset($validated['accommodation_id'])) {
            $roomType = RoomType::find(
                $validated['room_type_id'] ?? $room->room_type_id
            );
            $accommodation = Accommodation::find(
                $validated['accommodation_id'] ?? $room->accommodation_id
            );

            if (!$roomType->accommodations->contains($accommodation)) {
                throw ValidationException::withMessages([
                    'accommodation_id' => 'La acomodación no es válida para este tipo de habitación'
                ]);
            }
        }

        // Validar duplicados si se cambia tipo o acomodación
        if ((isset($validated['room_type_id']) || isset($validated['accommodation_id'])) &&
            $hotel->rooms()
                ->where('room_type_id', $validated['room_type_id'] ?? $room->room_type_id)
                ->where('accommodation_id', $validated['accommodation_id'] ?? $room->accommodation_id)
                ->where('id', '!=', $room->id)
                ->exists()) {
            throw ValidationException::withMessages([
                'room_type_id' => 'Esta combinación de tipo y acomodación ya existe para este hotel'
            ]);
        }

        // Validar total de habitaciones si se cambia cantidad
        if (isset($validated['quantity'])) {
            $currentTotal = $hotel->rooms()->sum('quantity') - $room->quantity;
            $newTotal = $currentTotal + $validated['quantity'];

            if ($newTotal > $hotel->total_rooms) {
                throw ValidationException::withMessages([
                    'quantity' => 'La cantidad excede el total de habitaciones del hotel'
                ]);
            }
        }

        $room->update($validated);

        return response()->json($room->load('roomType', 'accommodation'));
    }

    public function destroy(Hotel $hotel, HotelRoom $room)
    {
        if ($room->hotel_id !== $hotel->id) {
            abort(404);
        }

        $room->delete();
        return response()->json(null, 204);
    }
}
