<?php
namespace App\Contracts;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

interface AuthInterface
{
    public function user(Request $request): JsonResponse;
    public function login(Request $request): JsonResponse;
    public function verify(Request $request): JsonResponse;
    public function logout(Request $request): JsonResponse;
    public function forgetPassword(Request $request): JsonResponse;
    public function changePassword(Request $request): JsonResponse;
}

?>
