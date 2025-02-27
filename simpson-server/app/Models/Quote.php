<?php

namespace App\Models;

use App\Enums\CharacterDirection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'text',
        'character',
        'image_url',
        'character_direction'
    ];

    protected $casts = [
        'character_direction' => CharacterDirection::class,
    ];
}
