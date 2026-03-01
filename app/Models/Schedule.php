<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Schedule extends Model
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
        'in_process',
        'done'
    ];
    public static function pendingRowsToProcessed()
    {
       return self::select(['id',
       'route_id',
       'driver_id',
       'user_id',
       'price',
       'departure',
       'bus_id'])
       ->where('in_process', '=', '1')
        ->where('done', '!=', Carbon::now()->format('Y-m-d H:i:s'))
        ->limit(100)
        ->get()
        ->toArray();
    }
}
