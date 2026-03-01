<?php


namespace App\Traits;

use Carbon\Carbon;


trait ApiResponse
{

	protected function successResponse($data, string $message = null, int $code = 200)
	{
		return response()->json([
			'status' => true,
			'message' => $message,
			'data' => $data
		], $code);
	}

	protected function errorResponse(string $message = null, int $code = 400, $data = null)
	{
		return response()->json([
			'status' => false,
			'message' => $message,
			'data' => $data
		], $code);
	}

}
