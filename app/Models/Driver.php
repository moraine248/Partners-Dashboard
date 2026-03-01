<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $with = ['reviews'];
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'city',
        'address',
        'phone',
        'next_of_kin_name',
        'next_of_kin_phone',
        'next_of_kin_address',
    ];

    public function reviews()
    {
        return $this->hasMany(Review::class, 'driver_id');
    }
}
