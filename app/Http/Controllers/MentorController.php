<?php

namespace App\Http\Controllers;

use App\Http\Traits\HelperTrait;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Models\Course;
use App\Models\Category;
use App\Models\Content;
use App\Models\ContentOutline;
use App\Models\CourseOutline;
use App\Models\MentorZoomLink;
use App\Models\CourseParticipant;
use App\Models\CourseClassRoutine;
use App\Models\CourseFeature;
use App\Models\CourseMentor;
use App\Models\CourseFaq;
use App\Models\User;
use App\Models\MentorInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MentorController extends Controller
{
    use HelperTrait;
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

        if (!$mentor_id) {
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

    public function myCourseList(Request $request)
    {
        $user_id = $request->user()->id;
        $mentor = MentorInformation::where('user_id', $user_id)->first();
        $ids = CourseMentor::where('mentor_id', $mentor->id)->pluck('course_id');

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

    public function updateZoomLink(Request $request)
    {
        $user_id = $request->user()->id;
        $mentor = MentorInformation::where('user_id', $user_id)->first();

        $zoomLink = MentorZoomLink::where('mentor_id', $mentor->id)->first();
        
        if(!empty($zoomLink)){
            MentorZoomLink::where('id', $zoomLink->id)->update([
                'live_link' => $request->live_link,
            ]);
        }else{
            MentorZoomLink::create([
                'mentor_id' => $mentor->id,
                'live_link' => $request->live_link,
                'is_active' => true
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Link has been updated successfully',
            'data' => []
        ], 200);
    }

    public function getZoomLink(Request $request)
    {
        $user_id = $request->user()->id;
        $mentor = MentorInformation::where('user_id', $user_id)->first();

        $zoomLink = MentorZoomLink::where('mentor_id', $mentor->id)->first();
        
        return response()->json([
            'status' => true,
            'message' => 'Link has been created successfully',
            'data' => $zoomLink
        ], 200);
    }

    public function mentorSaveOrUpdate(Request $request)
    {
        try {
            $mentor = [
                'education' => $request->education,
                'institute' => $request->institute,
                'device_id' => $request->device_id,
                'referral_code' => $request->referral_code,
                'referred_code' => $request->referred_code,
                'alternative_contact_no' => $request->alternative_contact_no,
                'gender' => $request->gender,
                'bio' => $request->bio,
                'father_name' => $request->father_name,
                'mother_name' => $request->mother_name,
                'religion' => $request->religion,
                'marital_status' => $request->marital_status,
                'date_of_birth' => $request->date_of_birth,
                'profession' => $request->profession,
                'current_address' => $request->current_address,
                'permanent_address' => $request->permanent_address,
                'division_id' => $request->division_id,
                'district_id' => $request->district_id,
                'city_id' => $request->city_id,
                'area_id' => $request->area_id,
                'nid_no' => $request->nid_no,
                'birth_certificate_no' => $request->birth_certificate_no,
                'passport_no' => $request->passport_no,
                'intro_video' => $request->intro_video,
                'status' => $request->status,
                'is_foreigner' => $request->is_foreigner,
                'is_life_couch' => $request->is_life_couch,
                'is_host_staff' => $request->is_host_staff,
                'is_host_certified' => $request->is_host_certified,
                'is_active' => $request->is_active,
                'rating' => $request->rating,
                'approval_date' => $request->approval_date,
                'host_rank_number' => $request->host_rank_number,
                'blood_group' => $request->blood_group,
                'nid_no' => $request->nid_no,

            ];

            if (empty($request->id)) {
                $validateUser = Validator::make(
                    $request->all(),
                    [
                        'name' => 'required',
                        'organization_slug' => 'required',
                        'username' => 'required|unique:users,username,',
                        'email' => 'unique:users,email,' ,
                        'contact_no' =>'unique:users,contact_no,',
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

            if (empty($request->id)) {
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
                    'user_type' => "Expert",
                    'password' => Hash::make($request->password)
                ]);
                if ($request->hasFile('image')) {
                    $user->update([
                        'image' => $this->imageUpload($request, 'image', 'image'),
                    ]);
                }
                $mentorInfo = MentorInformation::create(
                    [
                        'name' => $user->name,
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'username' => $user->username,
                        'contact_no' => $user->contact_no,
                        'organization_slug' => $user->organization_slug,
                        'address' => $user->address,
                        'image' => $user->image,
                        'mentor_code' => $this->codeGenerator('MC', MentorInformation::class),
                    ]
                );
                $mentorInfo->update($mentor);
                return $this->apiResponse([], 'Mentor Created Successfully', true, 200);
            } else {
                $validateUser = Validator::make(
                    $request->all(),
                    [
                        'name' => 'required',
                        'email' => 'unique:users,email,' . $request->user_id,
                        'contact_no' => 'unique:users,contact_no,' . $request->user_id,
                    ]
                );
        
                if ($validateUser->fails()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'validation error',
                        'data' => $validateUser->errors()
                    ], 422);
                }
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

                $mentorInfo = MentorInformation::where('id', $request->id)->first();
                $mentorInfo->update([
                    'name' => $user->name,
                    'email' => $user->email,
                    'contact_no' => $user->contact_no,
                    'address' => $user->address,
                    'image' => $user->image,

                ]);
                $mentorInfo->update($mentor);
                DB::commit();
                return $this->apiResponse([], 'Mentor Updated Successfully', true, 200);
            }}
        } catch (\Throwable $th) {
            return $this->apiResponse($th->getMessage(), 'Something went wrong', false, 500);
        }
    }
    public function allMentorListAdmin(Request $request)
    {
        $mentorList = MentorInformation::latest()->get();
        return  $this->apiResponse($mentorList, 'Mentor List', true, 200);
    }
}
