<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Topic;
use App\Models\SchoolInformation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function schoolList(Request $request)
    {
        $school_list = SchoolInformation::select('id', 'title', 'details', 'address', 'email', 'phone_no', 'logo', 'contact_person', 'is_active')->where('is_active', true)->get();

        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' => $school_list
        ], 200);
    }

    public function adminSchoolList(Request $request)
    {
        $school_list = SchoolInformation::all();

        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' => $school_list
        ], 200);
    }

    public function saveOrUpdateSchool (Request $request)
    {
        try {
            $formData = json_decode($request->data, true);

            if(!$formData['email']){
                return response()->json([
                    'status' => false,
                    'message' => 'Please, enter valid email address!',
                    'data' => []
                ], 200);
            }

            if($formData['id']){
                $logo_url = null;
                if($request->hasFile('file')){
                    $image = $request->file('file');
                    $time = time();
                    $logo_image = "logo_" . $time . '.' . $image->getClientOriginalExtension();
                    $destination = 'uploads/school';
                    $image->move($destination, $logo_image);
                    $logo_url = $destination . '/' . $logo_image;
                }

                $school = SchoolInformation::where('id', $formData['id'])->update([
                    "title" => $formData['title'],
                    "details" => $formData['details'],
                    "address" => $formData['address'],
                    "phone_no" => $formData['phone_no'],
                    "contact_person" => $formData['contact_person'],
                    "is_active" => $formData['is_active']
                ]);

                if($request->hasFile('file')){
                    SchoolInformation::where('id', $formData['id'])->update([
                        'logo' => $logo_url
                    ]);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'School has been updated successfully',
                    'data' => []
                ], 200);

            } else {
                $isExist = SchoolInformation::where('title', $formData['title'])->first();
                $isEmailExist = SchoolInformation::where('email', $formData['email'])->first();
                if (empty($isExist) && empty($isEmailExist)) 
                {
                    $logo_url = null;
                    if($request->hasFile('file')){
                        $image = $request->file('file');
                        $time = time();
                        $logo_image = "logo_" . $time . '.' . $image->getClientOriginalExtension();
                        $destination = 'uploads/school';
                        $image->move($destination, $logo_image);
                        $logo_url = $destination . '/' . $logo_image;
                    }

                    $school = SchoolInformation::create([
                        "title" => $formData['title'],
                        "details" => $formData['details'],
                        "address" => $formData['address'],
                        "email" => $formData['email'],
                        "phone_no" => $formData['phone_no'],
                        "contact_person" => $formData['contact_person'],
                        "is_active" => $formData['is_active']
                    ]);

                    $user = User::create([
                        'name' => $formData['title'],
                        'email' => $formData['email'],
                        'contact_no' => $formData['phone_no'],
                        'school_id' => $school->id,
                        'address' => $formData['address'],
                        'institution' => $formData['title'],
                        'education' => null,
                        'user_type' => "SchoolAdmin",
                        'password' => Hash::make($formData['password'] ? $formData['password'] : '123456')
                    ]);

                    SchoolInformation::where('id', $school->id)->update([
                        'user_id' => $user->id
                    ]);

                    if($request->hasFile('file')){
                        SchoolInformation::where('id', $school->id)->update([
                            'logo' => $logo_url
                        ]);
                    }

                    if($request->hasFile('file')){
                        User::where('id', $user->id)->update([
                            'image' => $logo_url
                        ]);
                    }

                    return response()->json([
                        'status' => true,
                        'message' => 'School has been created successfully',
                        'data' => []
                    ], 200);
                }else{
                    return response()->json([
                        'status' => false,
                        'message' => 'School already Exist!',
                        'data' => []
                    ], 200);
                }
            }

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 200);
        }
    }
}
