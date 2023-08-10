<?php

namespace App\Http\Controllers;

use App\Http\Traits\HelperTrait;
use App\Models\Course;
use App\Models\Category;
use App\Models\Content;
use App\Models\MentorZoomLink;
use App\Models\ContentOutline;
use App\Models\CourseOutline;
use App\Models\CourseParticipant;
use App\Models\CourseClassRoutine;
use App\Models\CourseFeature;
use App\Models\CourseMentor;
use App\Models\CourseFaq;
use App\Models\ClassSchedule;
use App\Models\CourseStudentMapping;
use App\Models\MentorInformation;
use App\Models\StudentInformation;
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
                        'chapters.name as chapter_name',
                        'chapter_videos.title as video_title',
                        'chapter_videos.title_bn as video_title_bn',
                        'chapter_videos.author_name as video_author_name',
                        'chapter_videos.author_details as video_author_details',
                        'chapter_videos.raw_url as video_raw_url',
                        'chapter_videos.s3_url as video_s3_url',
                        'chapter_videos.youtube_url as video_youtube_url',
                        'chapter_videos.download_url as video_download_url',
                        'chapter_videos.duration as video_duration',
                        'chapter_videos.thumbnail as video_thumbnail',
                        'chapter_videos.is_free as video_is_free',
                        'chapter_scripts.title as script_title',
                        'chapter_scripts.title_bn as script_title_bn',
                        'chapter_scripts.raw_url as script_raw_url',
                        'chapter_scripts.is_free as script_is_free',
                        'chapter_quizzes.title as quiz_title',
                        'chapter_quizzes.title_bn as quiz_title_bn',
                        'chapter_quizzes.duration as quiz_duration',
                        'chapter_quizzes.is_free as quiz_is_free',
                    )
                        ->where('course_outlines.course_id', $course->id)
                        ->leftJoin('class_levels', 'class_levels.id', 'course_outlines.class_level_id')
                        ->leftJoin('subjects', 'subjects.id', 'course_outlines.subject_id')
                        ->leftJoin('chapters', 'chapters.id', 'course_outlines.chapter_id')
                        ->leftJoin('chapter_videos', 'chapter_videos.id', 'course_outlines.chapter_video_id')
                        ->leftJoin('chapter_scripts', 'chapter_scripts.id', 'course_outlines.chapter_script_id')
                        ->leftJoin('chapter_quizzes', 'chapter_quizzes.id', 'course_outlines.chapter_quiz_id')
                        ->get();

                    $course->course_routine = CourseClassRoutine::where('course_id', $course->id)->get();
                    $course->course_feature = CourseFeature::where('course_id', $course->id)->get();
                    $course->course_mentor = CourseMentor::select('course_mentors.*', 'mentor_informations.name', 'mentor_informations.education', 'mentor_informations.institute')
                        ->where('course_mentors.course_id', $course->id)
                        ->leftJoin('mentor_informations', 'mentor_informations.id', 'course_mentors.mentor_id')
                        ->get();
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
            'chapters.name as chapter_name',
            'chapter_videos.title as video_title',
            'chapter_videos.title_bn as video_title_bn',
            'chapter_videos.author_name as video_author_name',
            'chapter_videos.author_details as video_author_details',
            'chapter_videos.raw_url as video_raw_url',
            'chapter_videos.s3_url as video_s3_url',
            'chapter_videos.youtube_url as video_youtube_url',
            'chapter_videos.download_url as video_download_url',
            'chapter_videos.duration as video_duration',
            'chapter_videos.thumbnail as video_thumbnail',
            'chapter_videos.is_free as video_is_free',
            'chapter_scripts.title as script_title',
            'chapter_scripts.title_bn as script_title_bn',
            'chapter_scripts.raw_url as script_raw_url',
            'chapter_scripts.is_free as script_is_free',
            'chapter_quizzes.title as quiz_title',
            'chapter_quizzes.title_bn as quiz_title_bn',
            'chapter_quizzes.duration as quiz_duration',
            'chapter_quizzes.is_free as quiz_is_free',
        )
            ->where('course_outlines.course_id', $course_id)
            ->leftJoin('class_levels', 'class_levels.id', 'course_outlines.class_level_id')
            ->leftJoin('subjects', 'subjects.id', 'course_outlines.subject_id')
            ->leftJoin('chapters', 'chapters.id', 'course_outlines.chapter_id')
            ->leftJoin('chapter_videos', 'chapter_videos.id', 'course_outlines.chapter_video_id')
            ->leftJoin('chapter_scripts', 'chapter_scripts.id', 'course_outlines.chapter_script_id')
            ->leftJoin('chapter_quizzes', 'chapter_quizzes.id', 'course_outlines.chapter_quiz_id')
            ->get();

        $course->course_routine = CourseClassRoutine::where('course_id', $course_id)->get();
        $course->course_feature = CourseFeature::where('course_id', $course_id)->get();
        $course->course_mentor = CourseMentor::select('course_mentors.*', 'mentor_informations.name', 'mentor_informations.education', 'mentor_informations.institute')
            ->where('course_mentors.course_id', $course_id)
            ->leftJoin('mentor_informations', 'mentor_informations.id', 'course_mentors.mentor_id')
            ->get();

        $course->course_faq = CourseFaq::where('course_id', $course_id)->get();

        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' => $course
        ], 200);
    }

    public function courseListByID(Request $request)
    {
        $menu_id = $request->menu_id ? $request->menu_id : 0;

        if (!$menu_id) {
            return response()->json([
                'status' => false,
                'message' => 'Please, attach menu ID',
                'data' => []
            ], 422);
        }

        $menu = Category::where('id', $menu_id)->first();

        if (empty($menu)) {
            return response()->json([
                'status' => false,
                'message' => 'Menu not found!',
                'data' => []
            ], 404);
        }

        if ($menu->is_course) {
            $courses = Course::where('category_id', $menu->id)->orderBy('sequence', 'ASC')->get();
            $menu->courses = $courses;

            foreach ($courses as $course) {
                $course->course_outline = CourseOutline::select(
                    'course_outlines.*',
                    'class_levels.name as class_name',
                    'subjects.name as subject_name',
                    'chapters.name as chapter_name',
                    'chapter_videos.title as video_title',
                    'chapter_videos.title_bn as video_title_bn',
                    'chapter_videos.author_name as video_author_name',
                    'chapter_videos.author_details as video_author_details',
                    'chapter_videos.raw_url as video_raw_url',
                    'chapter_videos.s3_url as video_s3_url',
                    'chapter_videos.youtube_url as video_youtube_url',
                    'chapter_videos.download_url as video_download_url',
                    'chapter_videos.duration as video_duration',
                    'chapter_videos.thumbnail as video_thumbnail',
                    'chapter_videos.is_free as video_is_free',
                    'chapter_scripts.title as script_title',
                    'chapter_scripts.title_bn as script_title_bn',
                    'chapter_scripts.raw_url as script_raw_url',
                    'chapter_scripts.is_free as script_is_free',
                    'chapter_quizzes.title as quiz_title',
                    'chapter_quizzes.title_bn as quiz_title_bn',
                    'chapter_quizzes.duration as quiz_duration',
                    'chapter_quizzes.is_free as quiz_is_free',
                )
                    ->where('course_outlines.course_id', $course->id)
                    ->leftJoin('class_levels', 'class_levels.id', 'course_outlines.class_level_id')
                    ->leftJoin('subjects', 'subjects.id', 'course_outlines.subject_id')
                    ->leftJoin('chapters', 'chapters.id', 'course_outlines.chapter_id')
                    ->leftJoin('chapter_videos', 'chapter_videos.id', 'course_outlines.chapter_video_id')
                    ->leftJoin('chapter_scripts', 'chapter_scripts.id', 'course_outlines.chapter_script_id')
                    ->leftJoin('chapter_quizzes', 'chapter_quizzes.id', 'course_outlines.chapter_quiz_id')
                    ->get();

                $course->course_routine = CourseClassRoutine::where('course_id', $course->id)->get();
                $course->course_feature = CourseFeature::where('course_id', $course->id)->get();
                $course->course_mentor = CourseMentor::select('course_mentors.*', 'mentor_informations.name', 'mentor_informations.education', 'mentor_informations.institute')
                    ->where('course_mentors.course_id', $course->id)
                    ->leftJoin('mentor_informations', 'mentor_informations.id', 'course_mentors.mentor_id')
                    ->get();
                $course->course_faq = CourseFaq::where('course_id', $course->id)->get();
            }
        }

        if ($menu->is_content) {
            $content_list = Content::where('category_id', $menu->id)->get();
            $menu->contents = $content_list;

            foreach ($content_list as $content) {
                $content->content_outline = ContentOutline::select(
                    'content_outlines.*',
                    'class_levels.name as class_name',
                    'subjects.name as subject_name',
                    'chapters.name as chapter_name'
                )
                    ->where('content_outlines.content_id', $content->id)
                    ->leftJoin('class_levels', 'class_levels.id', 'content_outlines.class_level_id')
                    ->leftJoin('subjects', 'subjects.id', 'content_outlines.subject_id')
                    ->leftJoin('chapters', 'chapters.id', 'content_outlines.chapter_id')
                    ->get();
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Successful',
            'data' => $menu
        ], 200);
    }

    public function courseDetailsByUserID(Request $request)
    {
        $user_id = $request->user_id ? $request->user_id : 0;
        $course_id = $request->course_id ? $request->course_id : 0;

        if (!$course_id) {
            return response()->json([
                'status' => false,
                'message' => 'Please, attach Course ID',
                'data' => []
            ], 422);
        }

        $courses = Course::where('id', $course_id)->first();

        $is_exist = CourseParticipant::where('item_id', $course_id)->where('user_id', $user_id)->where('item_type', 'Course')->first();
        if (!empty($is_exist)) {
            $courses->is_purchased = true;
        } else {
            $courses->is_purchased = false;
        }

        $courses->course_outline = CourseOutline::select(
            'course_outlines.*',
            'class_levels.name as class_name',
            'subjects.name as subject_name',
            'chapters.name as chapter_name',
            'chapter_videos.title as video_title',
            'chapter_videos.title_bn as video_title_bn',
            'chapter_videos.author_name as video_author_name',
            'chapter_videos.author_details as video_author_details',
            'chapter_videos.raw_url as video_raw_url',
            'chapter_videos.s3_url as video_s3_url',
            'chapter_videos.youtube_url as video_youtube_url',
            'chapter_videos.download_url as video_download_url',
            'chapter_videos.duration as video_duration',
            'chapter_videos.thumbnail as video_thumbnail',
            'chapter_videos.is_free as video_is_free',
            'chapter_scripts.title as script_title',
            'chapter_scripts.title_bn as script_title_bn',
            'chapter_scripts.raw_url as script_raw_url',
            'chapter_scripts.is_free as script_is_free',
            'chapter_quizzes.title as quiz_title',
            'chapter_quizzes.title_bn as quiz_title_bn',
            'chapter_quizzes.duration as quiz_duration',
            'chapter_quizzes.is_free as quiz_is_free',
        )
            ->where('course_outlines.course_id', $course_id)
            ->leftJoin('class_levels', 'class_levels.id', 'course_outlines.class_level_id')
            ->leftJoin('subjects', 'subjects.id', 'course_outlines.subject_id')
            ->leftJoin('chapters', 'chapters.id', 'course_outlines.chapter_id')
            ->leftJoin('chapter_videos', 'chapter_videos.id', 'course_outlines.chapter_video_id')
            ->leftJoin('chapter_scripts', 'chapter_scripts.id', 'course_outlines.chapter_script_id')
            ->leftJoin('chapter_quizzes', 'chapter_quizzes.id', 'course_outlines.chapter_quiz_id')
            ->get();

        $courses->course_routine = CourseClassRoutine::where('course_id', $course_id)->get();
        $courses->course_feature = CourseFeature::where('course_id', $course_id)->get();
        $courses->course_mentor = CourseMentor::select('course_mentors.*', 'mentor_informations.name', 'mentor_informations.education', 'mentor_informations.institute')
            ->where('course_mentors.course_id', $course_id)
            ->leftJoin('mentor_informations', 'mentor_informations.id', 'course_mentors.mentor_id')
            ->get();
        $courses->course_faq = CourseFaq::where('course_id', $course_id)->get();

        return response()->json([
            'status' => true,
            'message' => 'Successful',
            'data' => $courses
        ], 200);
    }

    public function mentorStudentList(Request $request)
    {
        $user_id = $request->user()->id;
        $mentor = MentorInformation::where('user_id', $user_id)->first();

        $student = CourseStudentMapping::select(
            'course_student_mappings.id as mapping_id',
            'courses.title as course_title',
            'mentor_informations.name as mentor_name',
            'student_informations.name as student_name',
            'student_informations.contact_no as student_contact_no'
        )
            ->where('course_student_mappings.mentor_id', $mentor->id)
            ->leftJoin('courses', 'courses.id', 'course_student_mappings.course_id')
            ->leftJoin('mentor_informations', 'mentor_informations.id', 'course_student_mappings.mentor_id')
            ->leftJoin('student_informations', 'student_informations.id', 'course_student_mappings.student_id')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Successful',
            'data' => $student
        ], 200);
    }

    public function mentorClassScheduleList(Request $request)
    {
        $mapping_id = $request->mapping_id ? $request->mapping_id : 0;

        if (!$mapping_id) {
            return response()->json([
                'status' => false,
                'message' => 'Please, attach Class ID',
                'data' => []
            ], 422);
        }

        $class = ClassSchedule::select(
            'class_schedules.*',
            'courses.title as course_title',
            'mentor_informations.name as mentor_name',
            'student_informations.name as student_name',
            'student_informations.contact_no as student_contact_no'
        )
            ->where('class_schedules.course_student_mapping_id', $mapping_id)
            ->leftJoin('courses', 'courses.id', 'class_schedules.course_id')
            ->leftJoin('mentor_informations', 'mentor_informations.id', 'class_schedules.mentor_id')
            ->leftJoin('student_informations', 'student_informations.id', 'class_schedules.student_id')
            ->get();

        foreach ($class as $item) {
            $isToday = date('Ymd') == date('Ymd', strtotime($item->schedule_datetime));

            if ($isToday) {
                $item->can_join = true;
            } else {
                $item->can_join = false;
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Successful',
            'data' => $class
        ], 200);
    }

    public function mentorCompletedClassList(Request $request)
    {
        $user_id = $request->user()->id;
        $mentor = MentorInformation::where('user_id', $user_id)->first();

        $class = ClassSchedule::select(
            'class_schedules.*',
            'courses.title as course_title',
            'mentor_informations.name as mentor_name',
            'student_informations.name as student_name',
            'student_informations.contact_no as student_contact_no'
        )
            ->where('class_schedules.mentor_id', $mentor->id)
            ->where('class_schedules.has_completed', true)
            ->leftJoin('courses', 'courses.id', 'class_schedules.course_id')
            ->leftJoin('mentor_informations', 'mentor_informations.id', 'class_schedules.mentor_id')
            ->leftJoin('student_informations', 'student_informations.id', 'class_schedules.student_id')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Successful',
            'data' => $class
        ], 200);
    }

    public function studentClassList(Request $request)
    {
        $user_id = $request->user()->id;
        $student = StudentInformation::where('user_id', $user_id)->first();

        $class = ClassSchedule::select(
            'class_schedules.*',
            'courses.title as course_title',
            'mentor_informations.name as mentor_name',
            'student_informations.name as student_name',
            'student_informations.contact_no as student_contact_no'
        )
            ->where('class_schedules.student_id', $student->id)
            ->leftJoin('courses', 'courses.id', 'class_schedules.course_id')
            ->leftJoin('mentor_informations', 'mentor_informations.id', 'class_schedules.mentor_id')
            ->leftJoin('student_informations', 'student_informations.id', 'class_schedules.student_id')
            ->get();

        //$class_list = [];

        foreach ($class as $item) 
        {
            $isToday = date('Ymd') == date('Ymd', strtotime($item->schedule_datetime));

            if(!empty($zoomLink)){
                $item->join_link = $zoomLink->live_link;
            }else{
                $item->join_link = null;
            }
            
            if($isToday) {
                $item->can_join = true;
                //array_push($class_list, $item);
            }else{
                $item->can_join = false;
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Successful',
            'data' => $class
        ], 200);
    }

    public function mentorOngoingClassList(Request $request)
    {
        $user_id = $request->user()->id;
        $mentor = MentorInformation::where('user_id', $user_id)->first();

        $zoomLink = MentorZoomLink::where('mentor_id', $mentor->id)->first();

        $class = ClassSchedule::select(
            'class_schedules.*',
            'courses.title as course_title',
            'mentor_informations.name as mentor_name',
            'student_informations.name as student_name',
            'student_informations.contact_no as student_contact_no'
        )
            ->where('class_schedules.mentor_id', $mentor->id)
            ->where('class_schedules.has_completed', false)
            ->leftJoin('courses', 'courses.id', 'class_schedules.course_id')
            ->leftJoin('mentor_informations', 'mentor_informations.id', 'class_schedules.mentor_id')
            ->leftJoin('student_informations', 'student_informations.id', 'class_schedules.student_id')
            ->get();

        $class_list = [];

        foreach ($class as $item) {
            $isToday = date('Ymd') == date('Ymd', strtotime($item->schedule_datetime));

            if (!empty($zoomLink)) {
                $item->join_link = $zoomLink->live_link;
            } else {
                $item->join_link = null;
            }

            if ($isToday) {
                $item->can_join = true;
                array_push($class_list, $item);
            } else {
                $item->can_join = false;
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Successful',
            'data' => $class_list
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
                'is_only_note' => $request->is_only_note
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
                'course_outlines.is_only_note',
                'class_levels.name as class_name',
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

                $faq = CourseFaq::where('id', $request->id)->first();
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

    public function saveOrUpdateFeature(Request $request)
    {

        try {

            if (empty($request->id)) {

                $featureArr = json_decode($request->feature, true);
                if ($featureArr) {
                    $feature = [];
                    foreach ($featureArr as $key => $value) {
                        $feature[] = [
                            'title' => $value['title'],
                            'title_bn' => $value['title_bn'],
                            'course_id' => $value['course_id'],
                        ];
                    }
                    CourseFeature::insert($feature);
                }
                return $this->apiResponse([], 'Course feature Created Successfully', true, 201);
            } else {

                $feature = CourseFeature::where('id', $request->id)->first();
                $feature->update($request->all());
                return $this->apiResponse([], 'Course feature Updated Successfully', true, 200);
            }
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }

    public function featureList(Request $request)
    {
        $id = $request->id;
        $featureList = CourseFeature::where(
            'course_id',
            $id
        )->leftJoin('courses', 'courses.id', 'course_features.course_id')
            ->select(
                'course_features.title',
                'course_features.title_bn',
                'course_features.id',
                'course_features.course_id',
                'courses.title as course_title'
            )
            ->get();
        return $this->apiResponse($featureList, 'Feature List', true, 200);
    }

    public function featureDelete(Request $request)
    {
        try {
            CourseFeature::where('id', $request->id)->delete();
            return $this->apiResponse([], 'Course Feature Deleted Successfully', true, 200);
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }

    public function saveOrUpdateRoutine(Request $request)
    {
        try {
            if (empty($request->id)) {
                $routineArr = json_decode($request->routine, true);
                if ($routineArr) {
                    $routine = [];
                    foreach ($routineArr as $key => $value) {
                        $routine[] = [
                            'day' => $value['day'],
                            'class_title' => $value['class_title'],
                            'course_id' => $value['course_id'],
                            'is_note' => $value['is_note']
                        ];
                    }
                    CourseClassRoutine::insert($routine);
                }
                return $this->apiResponse([], 'Course routine Created Successfully', true, 201);
            } else {
                $routine = CourseClassRoutine::where('id', $request->id)->first();
                $routine->update($request->all());
                return $this->apiResponse([], 'Course routine Updated Successfully', true, 200);
            }
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }
    public function routineList(Request $request)
    {
        $id = $request->id;
        $RoutineList = CourseClassRoutine::where(
            'course_id',
            $id
        )->leftJoin('courses', 'courses.id', 'course_class_routines.course_id')
            ->select(
                'course_class_routines.day',
                'course_class_routines.class_title',
                'course_class_routines.is_note',
                'course_class_routines.id',
                'course_class_routines.course_id',
                'courses.title as course_title'
            )
            ->get();
        return $this->apiResponse($RoutineList, 'Routine List', true, 200);
    }

    public function routineDelete(Request $request)
    {
        try {
            CourseClassRoutine::where('id', $request->id)->delete();
            return $this->apiResponse([], 'Course Routine Deleted Successfully', true, 200);
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }

    public function courseMentorList(Request $request)
    {
        $mentorList = MentorInformation::select(
            'mentor_informations.id',
            'mentor_informations.name',
            'mentor_informations.user_id',
            'mentor_informations.email',
            'mentor_informations.username',
            'mentor_informations.contact_no',
        )->latest()->get();
        return $this->apiResponse($mentorList, 'Mentor List', true, 200);
    }
    public function courseStudentList(Request $request)
    {
        $mentorList = StudentInformation
            ::select(
                'student_informations.id',
                'student_informations.name',
                'student_informations.user_id',
                'student_informations.email',
                'student_informations.username',
                'student_informations.contact_no',
            )->latest()->get();
        return $this->apiResponse($mentorList, 'Student List', true, 200);
    }

    public function assignMentorByCourse(Request $request, $id)
    {
        try {
            $mentor = CourseMentor::where('course_id', $request->id)->first();
            return $this->apiResponse($mentor, 'Mentor Assigned Successfully', true, 200);
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }


    public function saveOrUpdateAssignMentor(Request $request)
    {
        try {
            if (empty($request->id)) {
                $mentorArr = json_decode($request->mentorArr, true);
                if ($mentorArr) {
                    $mentor = [];
                    foreach ($mentorArr as $key => $value) {
                        $mentor[] = [
                            'course_id' => $value['course_id'],
                            'mentor_id' => $value['mentor_id'],
                            'is_active' => $value['is_active'],
                        ];
                    }
                    CourseMentor::insert($mentor);
                }
                return $this->apiResponse([], 'Course mentor Created Successfully', true, 201);
            } else {
                $mentor = CourseMentor::where('id', $request->id)->first();
                $mentor->update($request->all());
                return $this->apiResponse([], 'Course mentor Updated Successfully', true, 200);
            }
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }

    public function mentorAssignList(Request $request)
    {
        $id = $request->id;
        $mentorList = CourseMentor::where(
            'course_id',
            $id
        )->leftJoin('courses', 'courses.id', 'course_mentors.course_id')
            ->leftJoin('mentor_informations', 'mentor_informations.id', 'course_mentors.mentor_id')
            ->select(
                'course_mentors.id',
                'course_mentors.course_id',
                'course_mentors.mentor_id',
                'course_mentors.is_active',
                'courses.title as course_title',
                'mentor_informations.name as mentor_name',
                'mentor_informations.email as mentor_email',
            )
            ->get();

        return $this->apiResponse($mentorList, 'Mentor assign List', true, 200);
    }


    public function mentorAssignDelete(Request $request)
    {
        try {
            CourseMentor::where('id', $request->id)->delete();
            return $this->apiResponse([], 'Course Mentor Deleted Successfully', true, 200);
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }


    public function saveOrUpdateStudentMapping(Request $request)
    {
        try {
            if (empty($request->id)) {
                $mapping = json_decode($request->mapping, true);
                if ($mapping) {
                    $studentMapping = [];
                    foreach ($mapping as $key => $value) {
                        $studentMapping[] = [
                            'course_id' => $value['course_id'],
                            'mentor_id' => $value['mentor_id'],
                            'is_active' => $value['is_active'],
                            'student_id' => $value['student_id'],
                        ];
                    }
                    CourseStudentMapping::insert($studentMapping);
                }
                return $this->apiResponse([], 'Course studentMapping Created Successfully', true, 201);
            } else {
                $studentMapping = CourseStudentMapping::where('id', $request->id)->first();
                $studentMapping->update([
                    'is_active' => $request->is_active,
                ]);
                return $this->apiResponse([], 'Course studentMapping Updated Successfully', true, 200);
            }
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }


    public function courseListForStudentMapping(Request $request)
    {
        $courseList = Course::select(
            'courses.id',
            'courses.title',
        )->latest()->get();
        return $this->apiResponse($courseList, 'Course List', true, 200);
    }

    public function mentorListByCourse(Request $request,$id)
    {
        $mentorList = CourseMentor::where('course_id', $id)
        ->leftJoin('mentor_informations', 'mentor_informations.id', 'course_mentors.mentor_id')

            ->select(
                'course_mentors.id',
                'course_mentors.course_id',
                'course_mentors.mentor_id',
                'course_mentors.is_active',
                'mentor_informations.name as mentor_name'
            )
            ->get();
        return $this->apiResponse($mentorList, 'Mentor List', true, 200);
    }



    public function studentMappingList(Request $request)
    {

        $studentMappingList = CourseStudentMapping::leftJoin('courses', 'courses.id', 'course_student_mappings.course_id')
            ->leftJoin('mentor_informations', 'mentor_informations.id', 'course_student_mappings.mentor_id')
            ->leftJoin('student_informations', 'student_informations.id', 'course_student_mappings.student_id')
            ->select(
                'course_student_mappings.id',
                'course_student_mappings.course_id',
                'course_student_mappings.mentor_id',
                'course_student_mappings.student_id',
                'course_student_mappings.is_active',
                'courses.title as course_title',
                'mentor_informations.name as mentor_name',
                'student_informations.name as student_name'
            )

            ->get();

        return $this->apiResponse($studentMappingList, 'Student Mapping List', true, 200);
    }
}
