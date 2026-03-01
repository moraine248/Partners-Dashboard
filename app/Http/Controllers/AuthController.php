<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Contracts\AuthInterface;
use App\Services\Authentication;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use ApiResponse;
    public $authentication;

    
    public function __construct(Authentication $authentication)
    {
        $this->authentication = $authentication;
    }
    public function login(Request $request){
            if(!Auth::attempt($request->only('email','password')))
            {
            return response([
                'message' => 'Incorrect Login Details',
                'status' => 401
            ]);
        }
        [$status, $token] = $this->authentication->login($request);
        if($status == 200)
            return $this->successResponse(['access_token' => $token], 'User login successful');
        elseif($status == 401)
            return $this->errorResponse('Account not verified',401);
        else    
            return $this->errorResponse('Invalid credentials', 401);
    }

    public function user(Request $request) {
        return $this->authentication->user($request);
    }


    public function verify(Request $request){
       $message =  $this->authentication->verify($request);
        return response()->json([
            'status' => 200,
            'message' => $message
        ]);
    } 
    public function logout(Request $request){
        $cookie = $this->authentication->logout($request);
        return response([
            'status' => 200,
            'message' => 'logged Out'
        ])->withCookie($cookie);
    }
    public function changePassword(Request $request)
    {
        $validator =  Validator::make($request->all(),[
            'current_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required|same:new_password'
        ]);

        if($validator->fails())
            return $this->errorResponse($validator->errors());

        return $this->authentication->changePassword($request);

    }

    public function forgetPassword(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email'
        ]);

        if($validator->fails())
            return $this->errorResponse($validator->errors(),401);

        return $this->authentication->forgetPassword($request);
    }


    public function resetPassword(Request $request){
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required|same:new_password'
        ]);

        if($validator->fails())
            return $this->errorResponse($validator->errors());

        return $this->authentication->resetPassword($request);
    }
}
