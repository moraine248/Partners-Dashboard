<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'route_id',
        'bus_id',
        'driver_id',
        'numbers_of_seats',
        'departure',
        'amount',
        'status',
        'user_id'
    ];
    
}
