<?php

namespace App\Http\Controllers;

use App\Models\ConsumerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PromotionalSiteController extends Controller
{
    public function clientInfoSave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'organization_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'data' => $validator->errors()
            ], 422);
        }

        ConsumerRequest::create(request()->all());
        return response()->json([
            'status' => true,
            'message' => 'Client Info Save Successful',
            'data' => []
        ], 200);
    }

    public function clientList(){
        $client_list = ConsumerRequest::select('*')
        ->latest()
        ->get();
        return response()->json([
            'status' => true,
            'message' => 'Client List Successful',
            'data' => $client_list
        ], 200);
    }
}
