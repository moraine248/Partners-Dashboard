<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PasswordReset extends Model
{
    use HasFactory;

    protected $primaryKey = 'email';

    public $guarded = [];

    public const UPDATED_AT = null;


    public static function generateUniqueToken(){
        $token = Str::random(30);
        if(self::where('token', $token)->exists())
            return self::generateUniqueToken();
        return $token;
    }


    public static function create(User $user, string $token){
        self::updateOrCreate(
            ['email' => $user->email],
            ['token' => $token]
        );
    }


    public static function verify(string $token){
        $valid =  self::where('token', $token)->first();
        if(! $valid)
            return ['status' => false, 'error' => 'Invalid token'];
        self::drop($token);
        return ['status' => true, 'email' => $valid->email];
    }

    public static function drop(string $token){
        $token = self::where('token', $token)->first();
        $token->delete();
    }
}
