<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('room_type_accommodations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained();
            $table->foreignId('accommodation_id')->constrained();
            $table->timestamps();
            
            $table->unique(['room_type_id', 'accommodation_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_type_accommodations');
    }
};
