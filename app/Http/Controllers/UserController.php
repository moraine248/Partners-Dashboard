<?php

namespace App\Http\Controllers;

use App\Contracts\UserInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Services\Authentication;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller implements UserInterface
{
    use ApiResponse;
    public $authService;

    public function __construct(Authentication $authService){
        $this->authService = $authService;
        $this->middleware('auth:sanctum',['only' => ['user','changePassword','logout']]);
      }

      public function index(): JsonResponse
        {
            $user = $this->authService->user();
            return response()->json([
                'status' => 200,
                'message' => 'Profile Created Succesfully',
                'data' => $user
               ]);       
        }

        public function create(Request $request, ): JsonResponse {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
                'confirm_password' => 'required|same:password',
            ]);
    
            if($validator->fails())
                return $this->errorResponse($validator->errors());
    
            $password = $request->password;
            $hashedPassword = bcrypt($password);
            $input = ['email' => $request->email, 'password' => $hashedPassword, 'name' => $request->name, 'phone' => $request->phone ?? null, 'nearest_busstop' => $request->nearest_busstop ?? null];
            return $this->authService->register($input);
        }

        
        
       

   
}
