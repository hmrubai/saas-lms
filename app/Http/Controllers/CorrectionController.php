<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Exception;
use App\Models\User;
use App\Models\Topic;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Correction;
use App\Models\PaymentDetail;
use App\Models\CorrectionRating;
use App\Models\TopicConsume;
use App\Models\PackageType;
use Illuminate\Http\Request;

class CorrectionController extends Controller
{
    public function getUTCTime($date) {
        return new Carbon($date, 'UTC');
    }

    public function checkAvailable(Request $request){
        $student_id = $request->user()->id;

        $topic = Topic::where('id', $request->topic_id)->first();
        if(empty($topic)){
            return response()->json([
                'status' => false,
                'message' => 'Topic not found!',
                'data' => []
            ], 200);
        }

        $package = Package::where('id', $request->package_id)->first();
        //Check is package exist or not
        if(empty($package)){
            return response()->json([
                'status' => false,
                'message' => 'Package not found!',
                'data' => []
            ], 200);
        }

        $correction_consume = TopicConsume::where('user_id', $student_id)->where('package_id', $request->package_id)->where('package_type_id', $topic->package_type_id)->get();

        if(!sizeof($correction_consume)){
            return response()->json([
                'status' => false,
                'message' => 'Correction limit exceeded!, Please check your package details!',
                'data' => []
            ], 200);
        }
        
        $is_expired = false; 
        foreach ($correction_consume as $item) {
            $packageDate = Carbon::parse($item->expiry_date);
            $now = Carbon::now();
            $is_expired = false;

            $balance = intval($item->balance);
            $consumme = intval($item->consumme);
    
            if ($now->gte($packageDate)) { 
                $is_expired = true;
            }

            if(($balance > $consumme) && !$is_expired){
                return response()->json([
                    'status' => true,
                    'message' => 'Proceed',
                    'data' => []
                ], 200);
            }
        }

        return response()->json([
            'status' => false,
            'message' => 'Correction limit exceeded!, Please check your package details!',
            'data' => []
        ], 200);
    }

    public function getCorrectionDetails($correction_id)
    {
        $correction_details = Correction::select(
            'corrections.id',
            'corrections.user_id',
            'corrections.expert_id',
            'corrections.topic_id',
            'corrections.package_id',
            'corrections.package_type_id as syllabus_id',
            'corrections.deadline',
            'corrections.is_accepted',
            'corrections.is_seen_by_expert',
            'corrections.is_seen_by_student',
            'corrections.is_student_resubmited',
            'corrections.student_correction',
            'corrections.expert_correction_note',
            'corrections.expert_correction_feedback',
            'corrections.grade',
            'corrections.student_rewrite',
            'corrections.expert_final_note',
            'corrections.student_correction_date',
            'corrections.expert_correction_date',
            'corrections.completed_date',
            'corrections.student_resubmission_date',
            'corrections.expert_final_note_date',
            'corrections.rating',
            'corrections.rating_note',
            'corrections.status',
            'topics.title as topic_title',
            'topics.hint',
            'users.name as student_name',
            'users.email as student_email',
            'users.image as student_image',
            'packages.title as package_name',
            'package_types.name as syllabus',
            'school_information.title as school_name'
        )
        ->leftJoin('users', 'users.id', 'corrections.user_id')
        ->leftJoin('topics', 'topics.id', 'corrections.topic_id')
        ->leftJoin('packages', 'packages.id', 'corrections.package_id')
        ->leftJoin('package_types', 'package_types.id', 'corrections.package_type_id')
        ->leftJoin('school_information', 'school_information.id', 'corrections.school_id')
        ->where('corrections.id', $correction_id)
        ->first();

        $correction_date = Carbon::now();
        $startTime = Carbon::parse(Carbon::now());
        $finishTime = Carbon::parse($correction_details->deadline);

        $now = Carbon::now();
        if ($now->gte($finishTime)) { 
            $correction_details->duration = 0;
        }else{
            $correction_details->duration = $finishTime->diffInSeconds($startTime);
        }

        if($correction_details->expert_id){
            $expert = User::where('id', $correction_details->expert_id)->first();
            if(!empty($expert)){
                $correction_details->expert_name = $expert->name;
                $correction_details->expert_email = $expert->email;
                $correction_details->expert_image = $expert->image;
            }else{
                $correction_details->expert_name = null;
                $correction_details->expert_email = null;
                $correction_details->expert_image = null;
            }
        }else{
            $correction_details->expert_name = null;
            $correction_details->expert_email = null;
            $correction_details->expert_image = null;
        }

        if($correction_details->admin_id){
            $admin = User::where('id', $correction_details->admin_id)->first();
            if(!empty($admin)){
                $correction_details->admin_name = $admin->name;
                $correction_details->admin_email = $admin->email;
                $correction_details->admin_image = $admin->image;
            }else{
                $correction_details->admin_name = null;
                $correction_details->admin_email = null;
                $correction_details->admin_image = null;
            }
        }else{
            $correction_details->admin_name = null;
            $correction_details->admin_email = null;
            $correction_details->admin_image = null;
        }

        return $correction_details;
    }

    public function submitCorrection(Request $request)
    {
        $student_id = $request->user()->id;

        $package = Package::where('id', $request->package_id)->first();
        //Check is package exist or not
        if(empty($package)){
            return response()->json([
                'status' => false,
                'message' => 'Package not found!',
                'data' => []
            ], 200);
        }

        $topic = Topic::where('id', $request->topic_id)->first();
        //Check is Topic exist or not
        if(empty($topic)){
            return response()->json([
                'status' => false,
                'message' => 'Topic not found!',
                'data' => []
            ], 200);
        }

        $package_type = PackageType::where('id', $topic->package_type_id)->first();
        //Check is Syllabus exist or not
        if(empty($package_type)){
            return response()->json([
                'status' => false,
                'message' => 'Syllabus not found!',
                'data' => []
            ], 200);
        }

        if(!$request->student_correction){
            return response()->json([
                'status' => false,
                'message' => 'Please, attach student correction!',
                'data' => []
            ], 200);
        }

        $user = User::where('id', $student_id)->first();

        $correction_consume = TopicConsume::where('user_id', $student_id)->where('package_id', $request->package_id)->where('package_type_id', $topic->package_type_id)->first();

        if(empty($correction_consume)){
            return response()->json([
                'status' => false,
                'message' => 'Please check your package details!',
                'data' => []
            ], 200);
        }

        if($correction_consume->balance <= $correction_consume->consumme){
            return response()->json([
                'status' => false,
                'message' => 'Correction limit exceeded!, Please check your package details!',
                'data' => []
            ], 200);
        }

        $packageDate = Carbon::parse($correction_consume->expiry_date);
        $now = Carbon::now();
        if ($now->gte($packageDate)) { 
            return response()->json([
                'status' => false,
                'message' => 'Your package has been expired. Please check your package details!',
                'data' => []
            ], 200);
        }

        $correction_date = Carbon::now();

        $correction = Correction::create([
            'user_id' => $student_id,
            'school_id' => $user->school_id,
            'topic_id' => $request->topic_id,
            'package_id' => $request->package_id,
            'package_type_id' => $topic->package_type_id,
            'status' => "Submitted",
            'student_correction' => $request->student_correction,
            'student_correction_date' => $correction_date
        ]);

        //Consume Update

        TopicConsume::where('id', $correction_consume->id)->update([
            "consumme" => $correction_consume->consumme + 1
        ]);

        $correction_details = Correction::select(
            'corrections.id',
            'corrections.user_id',
            'corrections.expert_id',
            'corrections.topic_id',
            'corrections.package_id',
            'corrections.package_type_id as syllabus_id',
            'corrections.deadline',
            'corrections.is_accepted',
            'corrections.is_seen_by_expert',
            'corrections.is_seen_by_student',
            'corrections.is_student_resubmited',
            'corrections.student_correction',
            'corrections.expert_correction_note',
            'corrections.expert_correction_feedback',
            'corrections.grade',
            'corrections.student_rewrite',
            'corrections.expert_final_note',
            'corrections.student_correction_date',
            'corrections.expert_correction_date',
            'corrections.completed_date',
            'corrections.student_resubmission_date',
            'corrections.expert_final_note_date',
            'corrections.rating',
            'corrections.rating_note',
            'corrections.status',
            'topics.title as topic_title',
            'topics.hint',
            'users.name as student_name',
            'users.email as student_email',
            'users.image as student_image',
            'packages.title as package_name',
            'package_types.name as syllabus',
            'school_information.title as school_name'
        )
        ->leftJoin('users', 'users.id', 'corrections.user_id')
        ->leftJoin('topics', 'topics.id', 'corrections.topic_id')
        ->leftJoin('packages', 'packages.id', 'corrections.package_id')
        ->leftJoin('package_types', 'package_types.id', 'corrections.package_type_id')
        ->leftJoin('school_information', 'school_information.id', 'corrections.school_id')
        ->where('corrections.id', $correction->id)
        ->first();

        $startTime = Carbon::parse(Carbon::now());
        $finishTime = Carbon::parse($correction_details->deadline);

        $now = Carbon::now();
        if ($now->gte($finishTime)) { 
            $correction_details->duration = 0;
        }else{
            $correction_details->duration = $finishTime->diffInSeconds($startTime);
        }

        $correction_details->expert_name = null;
        $correction_details->expert_email = null;
        $correction_details->expert_image = null;
        $correction_details->admin_name = null;
        $correction_details->admin_email = null;
        $correction_details->admin_image = null;

        return response()->json([
            'status' => true,
            'message' => 'Correction submitted successful.',
            'data' => $correction_details
        ], 200);
    }

    public function editCorrectionByStudent(Request $request)
    {
        $student_id = $request->user()->id;
        if(!$request->correction_id){
            return response()->json([
                'status' => false,
                'message' => 'Please, attach correction ID!',
                'data' => []
            ], 200);
        }

        $correction_exist = Correction::where('id', $request->correction_id)->where('status', "Submitted")->first();
        //Check is Correction exist or not
        if(empty($correction_exist)){
            return response()->json([
                'status' => false,
                'message' => 'You can not modify your correction! correction is Accepted by expert!',
                'data' => $this->getCorrectionDetails($request->correction_id)
            ], 200);
        }

        if(!$request->student_correction){
            return response()->json([
                'status' => false,
                'message' => 'Please, attach answer!',
                'data' => []
            ], 200);
        }

        if($correction_exist->user_id != $student_id){
            return response()->json([
                'status' => false,
                'message' => 'You can not modify someone correction!',
                'data' => []
            ], 200);
        }

        Correction::where('id', $request->correction_id)->update([
            'student_correction' => $request->student_correction
        ]);

        $correction_details = $this->getCorrectionDetails($request->correction_id);

        return response()->json([
            'status' => true,
            'message' => 'Correction updated successful.',
            'data' => $correction_details
        ], 200);
    }

    public function submitFeedback(Request $request)
    {
        $expert_id = $request->user()->id;
        if(!$request->correction_id){
            return response()->json([
                'status' => false,
                'message' => 'Please, attach correction ID!',
                'data' => []
            ], 200);
        }

        $correction_exist = Correction::where('id', $request->correction_id)->where('status', "Accepted")->first();
        //Check is package exist or not
        if(empty($correction_exist)){
            return response()->json([
                'status' => false,
                'message' => 'Correction not exist!!',
                'data' => []
            ], 200);
        }

        if($correction_exist->expert_id != $expert_id){
            return response()->json([
                'status' => false,
                'message' => 'Correction already accepted by another expert!',
                'data' => []
            ], 200);
        }

        $correction_date = Carbon::now();

        Correction::where('id', $request->correction_id)->update([
            'status' => "Corrected",
            'expert_correction_note' => $request->expert_correction_note,
            'expert_correction_feedback' => $request->expert_correction_feedback,
            'grade' => $request->grade ? $request->grade : "BelowSatisfaction",
            'expert_correction_date' => $correction_date,
            'completed_date' => $correction_date
        ]);

        $correction_details = $this->getCorrectionDetails($request->correction_id);

        return response()->json([
            'status' => true,
            'message' => 'Correction submitted successful.',
            'data' => $correction_details
        ], 200);
    }

    public function updateIsSeenByStudent(Request $request)
    {
        $student_id = $request->user()->id;
        if(!$request->correction_id){
            return response()->json([
                'status' => false,
                'message' => 'Please, attach correction ID!',
                'data' => []
            ], 200);
        }

        $correction_exist = Correction::where('id', $request->correction_id)->first();
        //Check is Correction exist or not
        if(empty($correction_exist)){
            return response()->json([
                'status' => false,
                'message' => 'You can not modify your correction! correction not found!',
                'data' => $this->getCorrectionDetails($request->correction_id)
            ], 200);
        }

        if($correction_exist->user_id != $student_id){
            return response()->json([
                'status' => false,
                'message' => 'You can not modify someone correction!',
                'data' => []
            ], 200);
        }

        Correction::where('id', $request->correction_id)->update([
            'is_seen_by_student' => true
        ]);

        $correction_details = $this->getCorrectionDetails($request->correction_id);

        return response()->json([
            'status' => true,
            'message' => 'Correction updated successful.',
            'data' => $correction_details
        ], 200);
    }

    public function getPendingCorrectionCount(Request $request)
    {
        $pending_list = Correction::where('is_accepted', true)->where('status', 'Accepted')->get();

        foreach ($pending_list as $item) {
            $correction_deadline = Carbon::parse($item->deadline);
            $now = Carbon::now();
            if ($now->gte($correction_deadline)) { 
                Correction::where('id', $item->id)->update([
                    "is_accepted" => false,
                    'status' => "Submitted",
                    'accepted_date' => null,
                    'deadline' => null,
                    'expert_id' => null
                ]);
            }
        }

        $total_pending = Correction::where('is_accepted', false)->where('status', 'Submitted')->get()->count();
        return response()->json([
            'status' => true,
            'message' => 'Successful.',
            'data' => [
                "pending" => $total_pending
            ]
        ], 200);
    }

    public function acceptPendingCorrection(Request $request)
    {   
        $expert_id = $request->user()->id;
        $pending_list = Correction::where('is_accepted', true)->where('status', 'Accepted')->get();

        foreach ($pending_list as $item) {
            $correction_deadline = Carbon::parse($item->deadline);
            $now = Carbon::now();
            if ($now->gte($correction_deadline)) { 
                Correction::where('id', $item->id)->update([
                    "is_accepted" => false,
                    'status' => "Submitted",
                    'accepted_date' => null,
                    'deadline' => null,
                    'expert_id' => null
                ]);
            }
        }

        $is_pending_exist = Correction::where('expert_id', $expert_id)->where('is_accepted', true)->where('status', 'Accepted')->get();
        if(sizeof($is_pending_exist)){
            return response()->json([
                'status' => false,
                'message' => 'You already have a pending correction. Please, solve it first!',
                'data' => []
            ], 200);
        }

        $pending = Correction::where('is_accepted', false)->first();

        if(empty($pending)){
            return response()->json([
                'status' => false,
                'message' => 'No pending correction is available! Please, try again.',
                'data' => []
            ], 200);
        }

        Correction::where('id', $pending->id)->update([
            "is_accepted" => true,
            'status' => "Accepted",
            'accepted_date' => Carbon::now(),
            'is_seen_by_expert' => true,
            'deadline' => Carbon::now()->addHours(2),
            'expert_id' => $expert_id
        ]);

        $correction_details = $this->getCorrectionDetails($pending->id);

        return response()->json([
            'status' => true,
            'message' => 'One correction has been accepted.',
            'data' => $correction_details
        ], 200);
    }

    public function getCorrectionList(Request $request)
    {
        $student_id = $request->user()->id;
        $correction_list = Correction::select(
            'corrections.id',
            'corrections.user_id',
            'corrections.expert_id',
            'corrections.topic_id',
            'corrections.package_id',
            'corrections.package_type_id as syllabus_id',
            'corrections.id',
            'corrections.deadline',
            'corrections.is_accepted',
            'corrections.is_seen_by_expert',
            'corrections.is_seen_by_student',
            'corrections.is_student_resubmited',
            'corrections.student_correction_date',
            'corrections.expert_correction_date',
            'corrections.status',
            'topics.title as topic_title',
            'users.name as student_name',
            'users.email as student_email',
            'users.image as student_image',
            'packages.title as package_name',
            'package_types.name as syllabus',
            'school_information.title as school_name'
        )
        ->leftJoin('users', 'users.id', 'corrections.user_id')
        ->leftJoin('topics', 'topics.id', 'corrections.topic_id')
        ->leftJoin('packages', 'packages.id', 'corrections.package_id')
        ->leftJoin('package_types', 'package_types.id', 'corrections.package_type_id')
        ->leftJoin('school_information', 'school_information.id', 'corrections.school_id')
        ->where('corrections.user_id', $student_id)
        ->orderBy('id', "DESC")
        ->get();

        foreach ($correction_list as $item) 
        {
            $startTime = Carbon::parse(Carbon::now());
            $finishTime = Carbon::parse($item->deadline);

            $now = Carbon::now();
            if ($now->gte($finishTime)) { 
                $item->duration = 0;
            }else{
                $item->duration = $finishTime->diffInSeconds($startTime);
            }

            if($item->expert_id){
                $expert = User::where('id', $item->expert_id)->first();
                if(!empty($expert)){
                    $item->expert_name = $expert->name;
                    $item->expert_email = $expert->email;
                    $item->expert_image = $expert->image;
                }else{
                    $item->expert_name = null;
                    $item->expert_email = null;
                    $item->expert_image = null;
                }
            }else{
                $item->expert_name = null;
                $item->expert_email = null;
                $item->expert_image = null;
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Correction list',
            'data' => $correction_list
        ], 200);
    }

    public function getCorrectionDetailsByID(Request $request)
    {
        $correction_id = $request->correction_id ? $request->correction_id : 0;
        if(!$correction_id){
            return response()->json([
                'status' => false,
                'message' => 'Please, attach correction ID!',
                'data' => []
            ], 200);
        }

        $correction_details = $this->getCorrectionDetails($correction_id);

        return response()->json([
            'status' => true,
            'message' => 'Correction Details',
            'data' => $correction_details
        ], 200);
    }

    public function getExpertCorrectionList(Request $request)
    {
        $expert_id = $request->user()->id;
        $correction_list = Correction::select(
            'corrections.id',
            'corrections.user_id',
            'corrections.expert_id',
            'corrections.topic_id',
            'corrections.package_id',
            'corrections.package_type_id as syllabus_id',
            'corrections.id',
            'corrections.deadline',
            'corrections.is_accepted',
            'corrections.is_seen_by_expert',
            'corrections.is_seen_by_student',
            'corrections.is_student_resubmited',
            'corrections.student_correction_date',
            'corrections.expert_correction_date',
            'corrections.status',
            'topics.title as topic_title',
            'users.name as student_name',
            'users.email as student_email',
            'users.image as student_image',
            'packages.title as package_name',
            'package_types.name as syllabus',
            'school_information.title as school_name'
        )
        ->leftJoin('users', 'users.id', 'corrections.user_id')
        ->leftJoin('topics', 'topics.id', 'corrections.topic_id')
        ->leftJoin('packages', 'packages.id', 'corrections.package_id')
        ->leftJoin('package_types', 'package_types.id', 'corrections.package_type_id')
        ->leftJoin('school_information', 'school_information.id', 'corrections.school_id')
        ->where('corrections.expert_id', $expert_id)
        ->orderBy('id', 'DESC')
        ->get();

        $expert = User::where('id', $expert_id)->first();

        foreach ($correction_list as $item) {

            $startTime = Carbon::parse(Carbon::now());
            $finishTime = Carbon::parse($item->deadline);

            $now = Carbon::now();
            if ($now->gte($finishTime)) { 
                $item->duration = 0;
            }else{
                $item->duration = $finishTime->diffInSeconds($startTime);
            }

            $item->expert_name = $expert->name;
            $item->expert_email = $expert->email;
            $item->expert_image = $expert->image;
        }

        return response()->json([
            'status' => true,
            'message' => 'Correction list',
            'data' => $correction_list
        ], 200);
    }

    public function editFeedback(Request $request)
    {
        $expert_id = $request->user()->id;
        if(!$request->correction_id){
            return response()->json([
                'status' => false,
                'message' => 'Please, attach correction ID!',
                'data' => []
            ], 200);
        }

        $correction_exist = Correction::where('id', $request->correction_id)->first();
        //Check is package exist or not
        if(empty($correction_exist)){
            return response()->json([
                'status' => false,
                'message' => 'Correction not exist!!',
                'data' => []
            ], 200);
        }

        if($correction_exist->expert_id != $expert_id){
            return response()->json([
                'status' => false,
                'message' => 'You can not modify another person\'s feedback!',
                'data' => []
            ], 200);
        }

        $correction_date = Carbon::now();

        Correction::where('id', $request->correction_id)->update([
            'expert_correction_note' => $request->expert_correction_note,
            'expert_correction_feedback' => $request->expert_correction_feedback,
            'grade' => $request->grade ? $request->grade : "BelowSatisfaction"
        ]);

        $correction_details = $this->getCorrectionDetails($request->correction_id);

        return response()->json([
            'status' => true,
            'message' => 'Correction updated successful.',
            'data' => $correction_details
        ], 200);
    }

    public function studentResubmission(Request $request)
    {
        $student_id = $request->user()->id;
        if(!$request->correction_id){
            return response()->json([
                'status' => false,
                'message' => 'Please, attach correction ID!',
                'data' => []
            ], 200);
        }

        $correction_exist = Correction::where('id', $request->correction_id)->first();
        //Check is package exist or not
        if(empty($correction_exist)){
            return response()->json([
                'status' => false,
                'message' => 'Correction not exist!!',
                'data' => []
            ], 200);
        }

        if($correction_exist->user_id != $student_id){
            return response()->json([
                'status' => false,
                'message' => 'You can not modify another person\'s answer!',
                'data' => []
            ], 200);
        }

        $correction_date = Carbon::now();

        Correction::where('id', $request->correction_id)->update([
            'student_rewrite' => $request->student_rewrite,
            'student_resubmission_date' => $correction_date,
            'is_student_resubmited' => true,
            'is_seen_by_student' => true
        ]);

        $correction_details = $this->getCorrectionDetails($request->correction_id);

        return response()->json([
            'status' => true,
            'message' => 'Correction updated successful.',
            'data' => $correction_details
        ], 200);
    }

    public function submitExpertFinalNote(Request $request)
    {
        $expert_id = $request->user()->id;
        if(!$request->correction_id){
            return response()->json([
                'status' => false,
                'message' => 'Please, attach correction ID!',
                'data' => []
            ], 200);
        }

        $correction_exist = Correction::where('id', $request->correction_id)->first();
        //Check is package exist or not
        if(empty($correction_exist)){
            return response()->json([
                'status' => false,
                'message' => 'Correction not exist!!',
                'data' => []
            ], 200);
        }

        if($correction_exist->expert_id != $expert_id){
            return response()->json([
                'status' => false,
                'message' => 'You can not modify another person\'s correction!',
                'data' => []
            ], 200);
        }

        if(!$correction_exist->is_student_resubmited){
            return response()->json([
                'status' => false,
                'message' => 'You can not submit your note!!',
                'data' => []
            ], 200);
        }

        $correction_date = Carbon::now();

        Correction::where('id', $request->correction_id)->update([
            'expert_final_note' => $request->expert_final_note,
            'expert_final_note_date' => $correction_date
        ]);

        $correction_details = $this->getCorrectionDetails($request->correction_id);

        return response()->json([
            'status' => true,
            'message' => 'Note submitted successful.',
            'data' => $correction_details
        ], 200);
    }

    public function submitStudentRating(Request $request)
    {
        $student_id = $request->user()->id;

        $correction_id = $request->correction_id ? $request->correction_id : 0;
        $rating = $request->rating ? $request->rating : 0;
        $rating_note = $request->rating_note ? $request->rating_note : null;

        if(!$rating || !$correction_id){
            return response()->json([
                'status' => false,
                'message' => 'Please, Enter rating & ID!',
                'data' => []
            ], 200);
        }

        $correction_details = Correction::where('id', $correction_id)->where('user_id', $student_id)->first();

        if(!empty($correction_details))
        {
            if($correction_details->status != "Corrected"){
                return response()->json([
                    'status' => false,
                    'message' => 'You can not submit rating!',
                    'data' => []
                ], 200);
            }

            Correction::where('id', $correction_id)->update([
                "rating" => $rating,
                "rating_note" => $rating_note ? $rating_note : null
            ]);

            $get_reting = CorrectionRating::where('expert_id', $correction_details->expert_id)->first();

            if(!empty($get_reting)){
                CorrectionRating::where('id', $get_reting->id)->update([
                    "total_rating"      => $get_reting->total_rating + $rating,
                    "total_correction"  => $get_reting->total_correction + 1,
                ]);

                $rating_sum = CorrectionRating::where('expert_id', $correction_details->expert_id)->first();

                CorrectionRating::where('id', $rating_sum->id)->update([
                    "rating_avg" => $rating_sum->total_rating / $rating_sum->total_correction
                ]);

            }else{
                CorrectionRating::create([
                    "expert_id"      => $correction_details->expert_id,
                    "total_rating"      => $rating,
                    "total_correction"  => 1,
                    "rating_avg"  => $rating
                ]);
            }

            $correction_details = $this->getCorrectionDetails($request->correction_id);

            return response()->json([
                'status' => true,
                'message' => 'Rating successful!',
                'data' => $correction_details
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Please, check details',
            'data' => []
        ], 200);

    }

    public function getMiniDashboardInfo (Request $request)
    {
        $expert_id = $request->user()->id;

        $date = date("Y-m-d H:i:s");

        $expiredList = Correction::where('status', 'Accepted')->where('deadline', '<', $date)->get();
        foreach ($expiredList as $item) {
            Correction::where('id', $item->id)->update([
                "is_accepted" => false,
                'status' => "Submitted",
                'accepted_date' => null,
                'deadline' => null,
                'expert_id' => null
            ]);
        }

        $is_expert = User::where('id', $expert_id)->where('user_type', 'Expert')->count();

        if($is_expert){

            //$all_correction_list = Correction::where('expert_id', $expert_id)->get();

            $point = Correction::where('expert_id', $expert_id)->where('status', "Corrected")->sum('rating');
            $rating_count = Correction::where('expert_id', $expert_id)->where('rating', ">", 0)->get()->count();

            $get_ratings = CorrectionRating::orderby('total_rating', "DESC")->get();
            $position = 0;
            foreach ($get_ratings as $key => $value) {
                if($value->expert_id == $expert_id){
                    $position = $key + 1;
                }
            }

            $total_available_correction = Correction::where('status', 'Submitted')->get()->count();

            $underCorrection = Correction::where('expert_id', $expert_id)->where('is_accepted', true)->where('status', 'Accepted')->get()->count();

            $totalCorrected = Correction::where('expert_id', $expert_id)->where('status', 'Corrected')->get()->count();

            $total_rating = 0.0;
            if($rating_count){
                $total_rating = $point/$rating_count;
            }

            $totalCorrectedToday = Correction::where('expert_id', $expert_id)->where('status', 'Corrected')->whereDate('completed_date', date("Y-m-d"))->get()->count();

            $lastCorrection = Correction::where('expert_id', $expert_id)->where('status', 'Corrected')->orderBy('completed_date', 'desc')->first();

            $date30Ago = Carbon::today()->subDays(30);
            $date7Ago = Carbon::today()->subDays(7);

            $totalCorrectedLast30Days = Correction::where('expert_id', $expert_id)->where('status', 'Corrected')->whereDate('completed_date', '>=', $date30Ago)->get()->count();
            $totalCorrectedLast7Days = Correction::where('expert_id', $expert_id)->where('status', 'Corrected')->whereDate('completed_date', '>=', $date7Ago)->get()->count();

            $totalCorrectedThisMonth = Correction::where('expert_id', $expert_id)->where('status', 'Corrected')
                ->whereMonth('completed_date', date('m'))
                ->whereYear('completed_date', date('Y'))
                ->get()->count();

            $totalCorrectedThisYear = Correction::where('expert_id', $expert_id)->where('status', 'Corrected')->whereYear('completed_date', date('Y'))
                ->get()->count();

            //$this->getUTCTime($lastCorrection->completed_date),

            $response = (Object) [
                "under_correction" => $underCorrection,
                "total_point" => $point,
                "total_correction" => $totalCorrected,
                "total_correction_today" => $totalCorrectedToday,
                "total_correction_last_7_days" => $totalCorrectedLast7Days,
                "total_correction_last_30_days" => $totalCorrectedLast30Days,
                "total_correction_this_month" => $totalCorrectedThisMonth,
                "total_correction_this_year" => $totalCorrectedThisYear,
                "last_correction_date" => is_null($lastCorrection) ? null : $lastCorrection->completed_date,
                "available_correction" => $total_available_correction,
                "rating" => round($total_rating, 2),
                "rating_count" => $rating_count,
                "position" => $position
            ];

        }else{
            $response = (Object) [
                "under_correction" => 0,
                "total_point" => 0,
                "total_correction" => 0,
                "total_correction_today" => 0,
                "total_correction_last_7_days" => 0,
                "total_correction_last_30_days" => 0,
                "total_correction_this_month" => 0,
                "total_correction_this_year" => 0,
                "last_correction_date" => null,
                "available_correction" => 0,
                "rating" => 0,
                "rating_count" => 0,
                "position" => 0
            ];
        }

        return response()->json([
            'status' => true,
            'message' => 'Dashboard successful.',
            'data' => $response
        ], 200);
    }

}
