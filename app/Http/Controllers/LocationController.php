<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Division;
use App\Models\Union;
use App\Models\Upazila;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function divisionList(Request $request)
    {
        //$user_id = $request->user()->id;

        $division = Division::all();

        return response()->json([
            'status' => true,
            'message' => "Successful",
            'data' => $division
        ], 200);
    }

    public function districtListByID(Request $request)
    {
        $division_id = $request->division_id;
        $district = District::where('division_id', $division_id)->get();

        return response()->json([
            'status' => true,
            'message' => "Successful",
            'data' => $district
        ], 200);
    }

    public function upazilaListByID(Request $request)
    {
        $district_id = $request->district_id;
        $upazila = Upazila::where('district_id', $district_id)->get();

        return response()->json([
            'status' => true,
            'message' => "Successful",
            'data' => $upazila
        ], 200);
    }

    public function unionListByID(Request $request)
    {
        $upazilla_id = $request->upazilla_id;
        $union = Union::where('upazilla_id', $upazilla_id)->get();

        return response()->json([
            'status' => true,
            'message' => "Successful",
            'data' => $union
        ], 200);
    }
}
