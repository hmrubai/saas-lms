<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\StudentInformation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    public function updateInterests(Request $request){
        $user_id = $request->user()->id;

        try {
            $validateUser = Validator::make($request->all(), 
            [
                'tags' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'data' => $validateUser->errors()
                ], 401);
            }

            $student = StudentInformation::where('user_id', $user_id)->first();

            if(empty($student)){
                return response()->json([
                    'status' => false,
                    'message' => 'Your account not found!',
                    'data' => []
                ], 404);
            }

            $all_tags = implode(",",$request->tags);

            $student->update([
                "interests" => $all_tags
            ]);

            Log::debug('Student Service: Tag Updated!');

            $user = User::where('id', $user_id)->first();

            $response_user = [
                'name' => $user->name, 
                'username'=> $user->username, 
                'interests' => explode(',',$student->interests),
                'user_type' => $user->user_type, 
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ];
    
            return response()->json([
                'status' => true,
                'message' => 'Tags updated successful',
                'data' => $response_user
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function studentDetails(Request $request){
        $user_id = $request->user()->id;
        $student = StudentInformation::where('user_id', $user_id)->first();
        return response()->json([
            'status' => true,
            'message' => 'Successful',
            'data' => $student
        ], 200);
    }
}
