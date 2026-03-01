<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Contracts\AuthInterface;
use App\Mail\OTP;
use App\Mail\ResetPassword;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class Authentication
{
    use ApiResponse;
    public function login(Request $request)
    {

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = User::where('email', $request->email)->first();

            if($user->email_verified_at == null)
                return [401, 'Account not verified'];
            $token =  $user->createToken('rabbit')->plainTextToken;
            return [200, $token];
        }
        return [400, 'Invalid credentials'];
    }

    public function register($input){
        User::create($input);
        $sendOtp = self::sendOTP($input['email']);
        return !$sendOtp['status'] ?
            $this->errorResponse($sendOtp['error']) :
            $this->successResponse(null,$sendOtp['message']);

    }

    public static  function sendOTP(string $email){
        try {

            $user = User::getByEmail($email);
            $otp = User::generateUniqueOTP();
            $expiresIn = Carbon::now()->addDays(30);

            $user->update([
                'email' => $email,
                'remember_token' => $otp,
                'token_expires_in' => $expiresIn,
            ]);

            Mail::to($email)->send(new OTP($user));
            return ['status' => True, 'message' => 'A verification email has been sent to your new email address.'];

        } catch (\Exception $e) {
            return ['status' => False, 'error' => 'Unable to send OTP: ' . $e->getMessage()];
        }
    }

    public static function sendResetPasswordLink(string $email){
        try {

            $user = User::getByEmail($email);
            $token = PasswordReset::generateUniqueToken();
            $link = env("APP_URL").$token;
            Mail::to($user->email)->send(new ResetPassword($user, $link));
            PasswordReset::create($user, $token);
            return ['status' => true, 'message' => 'A Reset mail has been sent to your email address'];

        } catch (\Exception $e) {
            return ['status' => False, 'error' => 'Unable to send link: ' . $e->getMessage()];
        }
    }

    public function user() {
        return Auth::user();
    }

    public function verify(Request $request){
        $user = User::getByEmail($request->email);
        if($user->remember_token == $request->otp && $user->token_expires_in >= Carbon::now()){
            $user->update([
                'remember_token' => null,
                'token_expires_in' => null,
                'email_verified_at' => Carbon::now()
            ]);
            return $this->successResponse(null,'User Verified Successfully');
        }

        return $this->errorResponse('Invalid/Expires Token');
    }

    public function logout(Request $request){
        $request->user()->deletePreviousToken();
    }

    public function forgetPassword(Request $request){

        $reset = self::sendResetPasswordLink($request->email);

        return !$reset['status'] ?
            $this->errorResponse($reset['error']) :
            $this->successResponse(null,$reset['message']);
    }

    public function changePassword(Request $request){

        $user = Auth::user();

        if(!Hash::check($request->current_password, $user->password))
            return $this->errorResponse('Invalid Password');

        User::updatePassword($user->email, $request->new_password);
        return $this->successResponse(null, 'Password updated successful');

    }

    public function resetPassword(Request $request){

        $valid = PasswordReset::verify($request->token);

        if(!$valid['status'])
            return $this->errorResponse($valid['error'],401);

        User::updatePassword($valid['email'], $request->new_password);
        return $this->successResponse(null, 'Password updated successful');

    }

}





