<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'id',
        'trip_id',
        'driver_id',
        'user_id',
        'message',
        'rating',
        'headline',
    ];
    use HasFactory;


    public function driver(){
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function trip(){
        return $this->belongsTo(Trip::class, 'trip_id');
    }
}
