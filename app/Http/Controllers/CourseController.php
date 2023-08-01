<?php

namespace App\Http\Controllers;
use App\Models\Course;
use App\Models\Category;
use App\Models\ContentOutline;
use App\Models\CourseOutline;
use App\Models\CourseClassRoutine;
use App\Models\CourseFeature;
use App\Models\CourseMentor;
use App\Models\CourseFaq;
use Illuminate\Http\Request;

class CourseController extends Controller
{

    public function courseListByCategory(Request $request)
    {
        $courseList = Course::where('category_id', $request->category_id)
            ->select('id', 'name')->get();
        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' => $courseList
        ], 200);
    }

    public function allCourseList(Request $request)
    {
        $menus = Category::all();
        foreach ($menus as $item) {

            if($item->is_course){
                $courses = Course::where('category_id', $item->id)->orderBy('sequence', 'ASC')->get();
                $item->courses = $courses;
    
                foreach ($courses as $course) {
                    $course->course_outline = CourseOutline::select(
                            'course_outlines.*', 
                            'class_levels.name as class_name', 
                            'subjects.name as subject_name',
                            'chapters.name as chapter_name'
                        )
                        ->where('course_outlines.course_id', $course->id)
                        ->leftJoin('class_levels', 'class_levels.id', 'course_outlines.class_level_id')
                        ->leftJoin('subjects', 'subjects.id', 'course_outlines.subject_id')
                        ->leftJoin('chapters', 'chapters.id', 'course_outlines.chapter_id')
                        ->get();
    
                    $course->course_routine = CourseClassRoutine::where('course_id', $course->id)->get();
                    $course->course_feature = CourseFeature::where('course_id', $course->id)->get();
                    $course->course_mentor = CourseMentor::where('course_id', $course->id)->get();
                    $course->course_faq = CourseFaq::where('course_id', $course->id)->get();
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' => $menus
        ], 200);
    }
}
