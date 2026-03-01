<?php
namespace App\Contracts;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

interface UserInterface
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse;

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request): JsonResponse;

}


?>
