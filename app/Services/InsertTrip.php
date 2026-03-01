<?php

namespace App\Services;

use App\Models\Trip;
use Illuminate\Support\Str;
use App\Contracts\InsertTripInterface;

class InsertTrip implements InsertTripInterface
{
    public static function insert(array $trips): void
    {
        Trip::insert($trips);
    }
    
}





