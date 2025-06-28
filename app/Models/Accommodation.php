<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Accommodation extends Model
{
     use HasFactory;

    protected $fillable = ['type'];

    public function roomTypes(): BelongsToMany
    {
        return $this->belongsToMany(RoomType::class, 'room_type_accommodations');
    }
}
