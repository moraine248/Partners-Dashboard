<?php

namespace App\Models;

use App\Models\Trip;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $with = ['trip'];

    protected $fillable = ['trip_id', 'user_id', 'status', 'booking_date'];

    public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }
}
