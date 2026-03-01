<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id', 'trip_id', 'amount', 'datetime' ];
    protected $with = ['trip'];
    use HasFactory;

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}
