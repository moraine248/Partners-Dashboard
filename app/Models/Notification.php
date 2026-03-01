<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['id', 'headline', 'description', 'datetime', 'status', 'user_id'];
    use HasFactory;
}
