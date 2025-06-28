<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function accommodations(): BelongsToMany
    {
        return $this->belongsToMany(Accommodation::class, 'room_type_accommodations');
    }
}
