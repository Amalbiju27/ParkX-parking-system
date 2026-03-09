<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'parking_space_id',
        'category_id',
        'slot_id',
        'vehicle_number',
        'entry_time',
        'exit_time',
        'penalty',
        'status'
    ];
}
