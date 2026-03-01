<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'remember_token',
        'token_expires_in',
        'nearest_busstop',
        'phone'


    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'token_expires_in',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'token_expires_in' => 'datetime',
    ];

    public function drivers(){

    }

    public function bookings(){
        return $this->hasMany(Booking::class, 'user_id');
    }

    public function customers(){

    }

    public function admin(){

    }


    public static function getByEmail(string $email){
        return self::where('email', $email)->first();
    }

    public static function verify(string $email){
        return self::where('email', $email)->update(['email_verified_at' => Carbon::now()]);
    }

    public static function updatePassword(string $email, string $password){

        $updatedPassword = bcrypt($password);
        self::where('email', $email)->update(['password' => $updatedPassword]);
    }

    public function deletePreviousToken()
    {
        $this->tokens()->delete();
    }

    public static function generateUniqueOTP(){

        $otp = random_int(1000, 9999);

        if(self::where('remember_token', $otp)->exists())
            return self::generateUniqueOTP();

        return $otp;
    }

    public function kyc(){
        return $this->hasOne(Kyc::class, 'user_id');
    }

}

