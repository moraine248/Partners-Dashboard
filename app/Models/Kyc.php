<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kyc extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'fullname', 'identity_type', 'identity_card', 'user_id'];

    
}
