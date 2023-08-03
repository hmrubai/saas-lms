<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Exception;
use App\Models\User;
use App\Models\MentorInformation;
use Illuminate\Http\Request;

class MentorController extends Controller
{
    public function allMentorList(Request $request)
    {
        $mentorList = MentorInformation::where('is_active', true)->get();

        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' => $mentorList
        ], 200);
    }

    public function mentorDetailsByID(Request $request)
    {
        $mentor_id = $request->mentor_id ? $request->mentor_id : 0;

        if(!$mentor_id){
            return response()->json([
                'status' => false,
                'message' => 'Please, attach Mentor ID',
                'data' => []
            ], 422);
        }

        $mentor = MentorInformation::where('id', $mentor_id)->first();

        return response()->json([
            'status' => true,
            'message' => 'Successful',
            'data' => $mentor
        ], 200);
    }
}
