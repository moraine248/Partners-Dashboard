<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;



    protected $fillable = [
        'id',
        'route_id',
        'driver_id',
        'user_id',
        'price',
        'departure',
        'bus_id',
    ];
    protected $with = ['route', 'reviews', 'bookings'];
    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'trip_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'trip_id');
    }
}
