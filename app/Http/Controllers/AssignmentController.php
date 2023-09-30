<?php

namespace App\Http\Controllers;

use App\Http\Traits\HelperTrait;
use App\Models\Assignment;
use App\Models\AssignmentQuiz;
use App\Models\AssignmentScript;
use App\Models\AssignmentVideo;
use App\Models\StudentAssignment;
use App\Models\ChapterQuiz;
use App\Models\CourseMentor;
use App\Models\CourseParticipant;
use App\Models\CourseStudentMapping;
use App\Models\MentorInformation;
use App\Models\StudentInformation;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    use HelperTrait;

    public function createAssignment(Request $request)
    {
        $user_id = $request->user()->id;
        $course_id = $request->course_id ? $request->course_id : 0;
        $student_ids = $request->student_id ? $request->student_id : [];

        $mentor = MentorInformation::where('user_id', $user_id)->first();

        if (!sizeof($student_ids)) {
            return response()->json([
                'status' => false,
                'message' => 'Please, Select Student!',
                'data' => []
            ], 422);
        }

        if (!$request->course_id) {
            return response()->json([
                'status' => false,
                'message' => 'Please, Select Course!',
                'data' => []
            ], 422);
        }

        if (!$request->title) {
            return response()->json([
                'status' => false,
                'message' => 'Please, Enter Assignment Title!',
                'data' => []
            ], 422);
        }

        if (!$request->deadline) {
            return response()->json([
                'status' => false,
                'message' => 'Please, Select Deadline!',
                'data' => []
            ], 422);
        }
        try {

            $assignment = Assignment::create([
                'mentor_id' => $mentor->id,
                'course_id' => $course_id,
                'title' => $request->title,
                'title_bn' => $request->title,
                'description' => $request->description,
                'publish_date' => null,
                'deadline' => $request->deadline
            ]);

            foreach ($student_ids as $student_id) {

                StudentAssignment::create([
                    'assignment_id' => $assignment->id,
                    'student_id' => $student_id,
                    'given_video' => sizeof($request->video),
                    'completed_video' => 0,
                    'given_quiz' => sizeof($request->quiz),
                    'completed_quiz' => 0,
                    'given_script' => sizeof($request->script),
                    'completed_script' => 0,
                    'total_progress' => 0,
                    'total_progress_parcentage' => 0
                ]);

                //Script
                foreach ($request->script as $script_item) {

                    AssignmentScript::create([
                        'assignment_id' => $assignment->id,
                        'student_id' => $student_id,
                        'chapter_script_id' => $script_item['id'],
                        'has_completed' => 0
                    ]);
                }
                
                //Video
                foreach ($request->video as $video_item) {
                    AssignmentVideo::create([
                        'assignment_id' => $assignment->id,
                        'student_id' => $student_id,
                        'chapter_video_id' => $video_item['id'],
                        'has_completed' => 0
                    ]);
                }

                //quiz
                foreach ($request->quiz as $quiz_item) {
                    AssignmentQuiz::create([
                        'assignment_id' => $assignment->id,
                        'student_id' => $student_id,
                        'chapter_quiz_id' => $quiz_item['id'],
                        'gained_marks' => 0,
                        'has_completed' => 0
                    ]);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Assignment created successful!',
                'data' => []
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function publishAssignment(Request $request)
    {
        $assignment_id = $request->assignment_id ? $request->assignment_id : 0;

        $user_id = $request->user()->id;
        $mentor = MentorInformation::where('user_id', $user_id)->first();

        if (!$assignment_id) {
            return response()->json([
                'status' => false,
                'message' => 'Please, Select Assignment ID!',
                'data' => []
            ], 422);
        }

        Assignment::where('mentor_id', $mentor->id)->where('id', $assignment_id)->update([
            "publish_date" => date('Y-m-d h:i:s'),
            "status" => "Ongoing"
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Assignment has been updated successful!',
            'data' => []
        ], 200);
    }

    public function assignmentList(Request $request)
    {
        $user_id = $request->user()->id;
        $mentor = MentorInformation::where('user_id', $user_id)->first();

        $assignments = Assignment::where('mentor_id', $mentor->id)->get();
        foreach ($assignments as $key => $item) {
            if($item->publish_date){
                $item->publish_date = $this->addHour($item->publish_date, 6);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Assignment list successful!',
            'data' => $assignments
        ], 200);
    }

    public function studentAssignmentList(Request $request)
    {
        $user_id = $request->user()->id;
        $student = StudentInformation::where('user_id', $user_id)->first();

        $assignment = StudentAssignment::select(
            'assignments.*',
            'student_assignments.given_video',
            'student_assignments.completed_video',
            'student_assignments.given_quiz',
            'student_assignments.completed_quiz',
            'student_assignments.given_script',
            'student_assignments.completed_script',
            'student_assignments.total_progress',
            'student_assignments.total_progress_parcentage',
        )
        ->where('student_assignments.student_id', $student->id)
        ->where('assignments.status', '!=', 'Unpublished')
        ->leftJoin('assignments', 'assignments.id', 'student_assignments.assignment_id')
        ->get();

        foreach ($assignment as $key => $item) {
            if($item->publish_date){
                $item->publish_date = $this->addHour($item->publish_date, 6);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Assignment list successful!',
            'data' => $assignment
        ], 200);
    }
}
