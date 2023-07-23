<?php

namespace App\Http\Traits;

use Validator;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Hash;

trait ApiResponseTrait
{
    protected function apiResponse($data = null, $message = null, $status = null, $statusCode = null)
    {
        $array = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($array, $statusCode);
    }
}
