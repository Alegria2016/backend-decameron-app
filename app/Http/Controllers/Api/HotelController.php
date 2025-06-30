<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotel;
use Illuminate\Support\Facades\Validator;

class HotelController extends Controller
{

   
   public function index()
    {
        $hotels = Hotel::with('rooms.roomType', 'rooms.accommodation')->get();
        return response()->json($hotels);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'nit' => 'required|string|max:20|unique:hotels',
            'total_rooms' => 'required|integer|min:1',
            'image' => 'nullable|string|max:255', // Assuming you want to store an image path
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $hotel = Hotel::create($validator->validated());

        return response()->json($hotel, 201);
    }

    public function show(Hotel $hotel)
    {
        return response()->json($hotel->load('rooms.roomType', 'rooms.accommodation'));
    }

    public function update(Request $request, Hotel $hotel)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string|max:255',
            'city' => 'sometimes|required|string|max:255',
            'nit' => 'sometimes|required|string|max:20|unique:hotels,nit,'.$hotel->id,
            'total_rooms' => 'sometimes|required|integer|min:1',
            'image' => 'nullable|string|max:255', // Assuming you want to store an image path
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $hotel->update($validator->validated());

        return response()->json($hotel);
    }

    public function destroy(Hotel $hotel)
    {
        $hotel->delete();
        return response()->json(null, 204);
    }
}
