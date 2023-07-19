<?php

namespace App\Http\Traits;

use Validator;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Hash;

trait ApiResponseTrait
{
    public $projectName = 'Library Management System.';
    protected function apiResponse($data = null, $message = null, $status = true, $statusCode = 200)
    {
     
        $array = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($array, $statusCode);
    }


}
