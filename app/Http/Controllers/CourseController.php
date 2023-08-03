<?php

namespace App\Http\Controllers;

use App\Http\Traits\HelperTrait;
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
    use HelperTrait;

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
        $menus = Category::where('is_course', true)->get();
        foreach ($menus as $item) {

            if ($item->is_course) {
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

    public function courseDetailsByID(Request $request)
    {
        $course_id = $request->course_id ? $request->course_id : 0;

        if (!$course_id) {
            return response()->json([
                'status' => false,
                'message' => 'Please, attach menu ID',
                'data' => []
            ], 422);
        }

        $course = Course::where('id', $course_id)->orderBy('sequence', 'ASC')->first();

        $course->course_outline = CourseOutline::select(
            'course_outlines.*',
            'class_levels.name as class_name',
            'subjects.name as subject_name',
            'chapters.name as chapter_name'
        )
            ->where('course_outlines.course_id', $course_id)
            ->leftJoin('class_levels', 'class_levels.id', 'course_outlines.class_level_id')
            ->leftJoin('subjects', 'subjects.id', 'course_outlines.subject_id')
            ->leftJoin('chapters', 'chapters.id', 'course_outlines.chapter_id')
            ->get();

        $course->course_routine = CourseClassRoutine::where('course_id', $course_id)->get();
        $course->course_feature = CourseFeature::where('course_id', $course_id)->get();
        $course->course_mentor = CourseMentor::where('course_id', $course_id)->get();
        $course->course_faq = CourseFaq::where('course_id', $course_id)->get();

        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' => $course
        ], 200);
    }


    public function saveOrUpdateCourse(Request $request)
    {
        try {
            $course = [
                'title' => $request->title,
                'title_bn' => $request->title_bn,
                'category_id' => $request->category_id,
                'gp_product_id' => $request->gp_product_id,
                'youtube_url' => $request->youtube_url,
                'description' => $request->description,
                'number_of_enrolled'    => $request->number_of_enrolled,
                'regular_price' => $request->regular_price,
                'sale_price'    => $request->sale_price,
                'discount_percentage'   => $request->discount_percentage,
                'rating'    => $request->rating,
                'has_life_coach'    => $request->has_life_coach,
                'is_active' => $request->is_active,
                'is_free'   => $request->is_free,
                'sequence'  => $request->sequence,
                'appeared_from' => $request->appeared_from,
                'appeared_to'  => $request->appeared_to,
            ];

            if (empty($request->id)) {
                $courseList = Course::create($course);
                if ($request->hasFile('icon')) {
                    $courseList->update([
                        'icon' => $this->imageUpload($request, 'icon', 'icon'),
                        'thumbnail' => $this->imageUpload($request, 'thumbnail', 'thumbnail'),
                    ]);
                }
                return $this->apiResponse([], 'Course Created Successfully', true, 201);
            } else {

                $class = Course::where('id', $request->id)->first();
                if ($request->hasFile('icon')) {
                    Course::where('id', $request->id)->update([
                        'icon' => $this->imageUpload($request, 'icon', 'icon', $class->icon),
                        'thumbnail' => $this->imageUpload($request, 'thumbnail', 'thumbnail', $class->thumbnail)
                    ]);
                }

                $class->update($course);
                return $this->apiResponse([], 'Course Updated Successfully', true, 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function courseList()
    {
        $courseList = Course::leftJoin('categories', 'categories.id', 'courses.category_id')
            ->select('courses.*', 'categories.name as category_name')
            ->orderBy('courses.sequence', 'ASC')
            ->get();
        return $this->apiResponse($courseList, 'Course List', true, 200);
    }

    public function saveOrUpdateCourseOutline(Request $request)
    {
        try {
            $course = [
                'title' => $request->title,
                'title_bn' => $request->title_bn,
                'course_id' => $request->course_id,
                'class_level_id' => $request->class_level_id,
                'subject_id'    => $request->subject_id,
                'chapter_id'   => $request->chapter_id,
                'chapter_script_id' => $request->chapter_script_id,
                'chapter_video_id' => $request->chapter_video_id,
                'chapter_quiz_id' => $request->chapter_quiz_id,
                'is_free'  => $request->is_free,
                'sequence' => $request->sequence,
                'is_active ' => $request->is_active,
            ];

            if (empty($request->id)) {
                CourseOutline::create($course);

                return $this->apiResponse([], 'Course Outline Created Successfully', true, 201);
            } else {
                $class = CourseOutline::where('id', $request->id)->first();
                $class->update($course);
                return $this->apiResponse([], 'Course Outline Updated Successfully', true, 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function courseOutlineList(Request $request)
    {
        $id = $request->id;
        $courseOutlineList = CourseOutline::where(
            'course_id',
            $id
        )->leftJoin('class_levels', 'class_levels.id', 'course_outlines.class_level_id')
            ->leftJoin('subjects', 'subjects.id', 'course_outlines.subject_id')
            ->leftJoin('chapters', 'chapters.id', 'course_outlines.chapter_id')
            ->leftJoin('chapter_scripts', 'chapter_scripts.id', 'course_outlines.chapter_script_id')
            ->leftJoin('chapter_videos', 'chapter_videos.id', 'course_outlines.chapter_video_id')
            ->leftJoin('chapter_quizzes', 'chapter_quizzes.id', 'course_outlines.chapter_quiz_id')
            ->leftJoin('courses', 'courses.id', 'course_outlines.course_id')
            ->select(
                'course_outlines.title',
                'course_outlines.title_bn',
                'course_outlines.id',
                'course_outlines.course_id',
                'course_outlines.class_level_id',
                'course_outlines.subject_id',
                'course_outlines.chapter_id',
                'course_outlines.chapter_script_id',
                'course_outlines.chapter_video_id',
                'course_outlines.chapter_quiz_id',
                'course_outlines.is_free',
                'course_outlines.sequence',
                'course_outlines.is_active',
                'class_levels.name as class_level_name',
                'subjects.name as subject_name',
                'chapters.name as chapter_name',
                'chapter_scripts.title as chapter_script_title',
                'chapter_videos.title as chapter_video_title',
                'chapter_quizzes.title as chapter_quiz_title',
                'courses.title as course_title'
            )


            ->get();
        return $this->apiResponse($courseOutlineList, 'Course Outline List', true, 200);
    }
    public function courseOutlineDelete(Request $request)
    {
        try {
            CourseOutline::where('id', $request->id)->delete();
            return $this->apiResponse([], 'Course Outline Deleted Successfully', true, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function faqList(Request $request)
    {
        $id = $request->id;
        $faqList = CourseFaq::where(
            'course_id',
            $id
        )->leftJoin('courses', 'courses.id', 'course_faqs.course_id')
            ->select(
                'course_faqs.title',
                'course_faqs.answer',
                'course_faqs.id',
                'course_faqs.course_id',
                'course_faqs.is_active',
                'courses.title as course_title'
            )
            ->get();
        return $this->apiResponse($faqList, 'FAQ List', true, 200);
    }

    public function saveOrUpdateFaq(Request $request)
    {

        try {

            if (empty($request->id)) {

                $faqArr = json_decode($request->faq, true);
                if ($faqArr) {
                    $faq = [];
                    foreach ($faqArr as $key => $value) {
                        $faq[] = [
                            'title' => $value['title'],
                            'answer' => $value['answer'],
                            'course_id' => $value['course_id'],
                            'is_active' => $value['is_active'],
                        ];
                    }
                    CourseFaq::insert($faq);
                }
                return $this->apiResponse([], 'Course FAQ Created Successfully', true, 201);

            } else {

                $faq= CourseFaq::where('id', $request->id)->first();
                $faq->update($request->all());
                return $this->apiResponse([], 'Course FAQ Updated Successfully', true, 200);
            }
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }

    public function faqDelete(Request $request)
    {
        try {
            CourseFaq::where('id', $request->id)->delete();
            return $this->apiResponse([], 'Course FAQ Deleted Successfully', true, 200);
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }
}
