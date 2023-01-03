<?php

namespace App\Traits;

trait ApiResponse{

    protected function successResponse($data, $message = null, $code = 200)
	{
		return response()->json([
			'success'=> true,
			'message' => $message,
			'data' => $data
		], $code);
	}

	protected function errorResponse($message = null, $code)
	{
		return response()->json([
			'success'=> false,
			'message' => $message,
		], $code);
	}

}
