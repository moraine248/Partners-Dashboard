<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'logo', 'name', 'address', 'country', 'state', 'phone',
        'bank_name', 'account_number', 'email', 'service',
        'website', 'linkedin', 'user_id'
    ];
}
