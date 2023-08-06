<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Traits\HelperTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Models\User;
use App\Models\Course;
use App\Models\CourseParticipant;
use App\Models\StudentInformation;

class StudentController extends Controller
{
    use HelperTrait;
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

    public function myCourseList(Request $request)
    {
        $user_id = $request->user()->id;
        $student = StudentInformation::where('user_id', $user_id)->first();
        $ids = CourseParticipant::where('user_id', $user_id)->where('item_type', 'Course')->pluck('item_id');

        $courses = Course::select('courses.*', 'categories.name as category_name')
        ->whereIn('courses.id', $ids)
        ->leftJoin('categories', 'categories.id', 'courses.id')
        ->orderBy('courses.sequence', 'ASC')
        ->get();

        return response()->json([
            'status' => true,
            'message' => 'Successful',
            'data' => $courses
        ], 200);
    }

    public function studentSaveOrUpdate(Request $request)
    {
        $validateUser = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'organization_slug' => 'required',
                'username' => 'required|unique:users,username,' . $request->user_id,
                'email' => 'required|email|unique:users,email,' . $request->user_id,
                'contact_no' => 'unique:users,contact_no,' . $request->user_id,
                'password' => 'required'
            ]
        );

        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'data' => $validateUser->errors()
            ], 422);
        }
      
        try {
            $student = [
                'education' => $request->education,
                'institute' => $request->institute,
                'organization_slug'=>$request->organization_slug,
                'device_id'=>$request->device_id,
                'alternative_contact_no'=>$request->alternative_contact_no,
                'gender'=>$request->gender,
                'blood_group'=>$request->blood_group,
                'bio'=>$request->bio,
                'father_name'=>$request->father_name,
                'mother_name'=>$request->mother_name,
                'religion'=>$request->religion,
                'marital_status'=>$request->marital_status,
                'date_of_birth'=>$request->date_of_birth,
                'current_address'=>$request->current_address,
                'permanent_address'=>$request->permanent_address,
                'interests'=>$request->interests,
                'division_id'=> $request->division_id,
                'city_id'=> $request->city_id,
                'area_id'=> $request->area_id,
                'nid_no'=>$request->nid_no,
                'birth_certificate_no'=>$request->birth_certificate_no,
                'passport_no'=>$request->passport_no,
                'status' => $request->status,
                'is_foreigner' => $request->is_foreigner,
                'is_active' => $request->is_active,
                'rating' => $request->rating,
            ];

            if (empty($request->id)) {
                DB::beginTransaction();
                if ($request->email) {
                    $is_exist = User::where('email', $request->email)->first();
                    if (!empty($is_exist)) {
                        return response()->json([
                            'status' => true,
                            'message' => 'Email address already been used! Please use another email',
                            'data' => []
                        ], 422);
                    }
                }
    
                if ($request->contact_no) {
                    $is_exist = User::where('contact_no', $request->contact_no)->first();
                    if (!empty($is_exist)) {
                        return response()->json([
                            'status' => true,
                            'message' => 'Contact No already been used! Please use another number',
                            'data' => []
                        ], 422);
                    }
                }
    
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'username' => $request->username,
                    'contact_no' => $request->contact_no,
                    'organization_slug' => $request->organization_slug,
                    'address' => $request->address,
                    'user_type' => "Student",
                    'password' => Hash::make($request->password)
                ]);
                if ($request->hasFile('image')) {
                    $user->update([
                        'image' => $this->imageUpload($request, 'image', 'image'),
                    ]);
                }
                $studentInfo = StudentInformation::create(
                    [
                        'name' => $user->name,
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'username' => $user->username,
                        'contact_no' => $user->contact_no,
                        'organization_slug' => $user->organization_slug,
                        'address' => $user->address,
                        'image' => $user->image,
                        'student_code'=>$this->codeGenerator('SC', StudentInformation::class)
                    ]
                );
                $studentInfo->update($student);
                DB::commit();
                return $this->apiResponse([], 'Student Created Successfully', true, 200);
            } else {
                DB::beginTransaction();
                $user = User::where('id', $request->user_id)->first();
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'contact_no' => $request->contact_no,
                    'address' => $request->address,
                ]);

                if ($request->hasFile('image')) {
                    $user->update([
                        'image' => $this->imageUpload($request, 'image', 'image'),
                    ]);
                }

                $studentInfo = StudentInformation::where('id', $request->id)->first();
                $studentInfo->update([
                    'name' => $user->name,
                    'email' => $user->email,
                    'contact_no' => $user->contact_no,
                    'address' => $user->address,
                    'image' => $user->image,

                ]);
                $studentInfo->update($student);
                DB::commit();
                return $this->apiResponse([], 'Student Updated Successfully', true, 200);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->apiResponse($th->getMessage(), 'Something went wrong', false, 500);
        }
    }




}
