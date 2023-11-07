<?php

namespace App\Http\Controllers;

use App\Http\Traits\HelperTrait;
use App\Models\Course;
use App\Models\Category;
use App\Models\Content;
use App\Models\ChapterQuiz;
use App\Models\QuizQuestionSet;
use App\Models\ChapterQuizQuestion;
use App\Models\ChapterQuizWrittenQuestion;
use App\Models\ChapterQuizResult;
use App\Models\ChapterQuizSubject;
use App\Models\ChapterQuizResultAnswer;
use App\Models\ChapterQuizSubjectWiseResult;
use App\Models\ChapterQuizWrittenMark;
use App\Models\ChapterQuizWrittenAttachment;
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
use App\Models\CourseType;
use App\Models\MentorInformation;
use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\StudentInformation;
use App\Models\StudentJoinHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function courseListForFilter(){

        $courseList = Course::select('id', 'title')->get();
        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' => $courseList
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

        $content_list = [];
        $sub_content_list = [];
        $count = 0;
        $title_list = $courses->course_outline->where('is_only_note', true)->pluck('title');

        foreach ($courses->course_outline as $key => $item) {

            if($key > 0 && $item->is_only_note){
                array_push($content_list, ['title' => $title_list[$count], 'content' => $sub_content_list]);
                $count++;
                $sub_content_list = [];
            }
            else{
                array_push($sub_content_list, $item);
            }

            $quiz = null;
            if ($item->chapter_quiz_id) {
                //$set = QuizQuestionSet::inRandomOrder()->first();
                $quiz = ChapterQuiz::where('id', $item->chapter_quiz_id)->first();

                // $subject_list = ChapterQuizSubject::where('chapter_quiz_id', $item->chapter_quiz_id)->get();
            
                // $questions = [];
                // foreach ($subject_list as $subject) 
                // {
                //     $set_question = ChapterQuizQuestion::inRandomOrder()
                //     ->where('chapter_quiz_id', $item->chapter_quiz_id)
                //     ->where('question_set_id', $set->id)
                //     ->where('chapter_quiz_subject_id', $subject->id)
                //     ->limit($subject->no_of_question)
                //     ->get();

                //     foreach ($set_question as $row) {
                //         array_push($questions, $row);
                //     }
                // }
                // $quiz->questions = $questions;
            }

            $item->quiz_details = $quiz;
        }

        array_push($content_list, ['title' => $title_list[$count], 'content' => $sub_content_list]);
        $courses->structured_outline = $content_list;

        $courses->course_routine = CourseClassRoutine::where('course_id', $course_id)->get();
        $courses->course_feature = CourseFeature::where('course_id', $course_id)->get();
        $courses->course_mentor = CourseMentor::select('course_mentors.*', 'mentor_informations.name', 'mentor_informations.education', 'mentor_informations.institute', 'mentor_informations.image as mentor_image')
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

    public function courseDetailsByUserIDV2(Request $request)
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

        $content_list = [];
        $sub_content_list = [];
        
        $count = 0;

        $title_list = $courses->course_outline->where('is_only_note', true)->pluck('title');

        foreach ($courses->course_outline as $key => $item) {

            if($key > 0 && $item->is_only_note){
                array_push($content_list, ['title' => $title_list[$count], 'content' => $sub_content_list]);
                $count++;
                $sub_content_list = [];
            }
            else{
                array_push($sub_content_list, $item);
            }

            // $quiz = null;
            // if ($item->chapter_quiz_id) {
            //     $set = QuizQuestionSet::inRandomOrder()->first();
            //     $quiz = ChapterQuiz::where('id', $item->chapter_quiz_id)->first();

            //     $subject_list = ChapterQuizSubject::where('chapter_quiz_id', $item->chapter_quiz_id)->get();
            
            //     $questions = [];
            //     foreach ($subject_list as $subject) 
            //     {
            //         $set_question = ChapterQuizQuestion::inRandomOrder()
            //         ->where('chapter_quiz_id', $item->chapter_quiz_id)
            //         ->where('question_set_id', $set->id)
            //         ->where('chapter_quiz_subject_id', $subject->id)
            //         ->limit($subject->no_of_question)
            //         ->get();

            //         foreach ($set_question as $row) {
            //             array_push($questions, $row);
            //         }
            //     }
            //     $quiz->questions = $questions;
            // }

            // $item->quiz_details = $quiz;
        }

        array_push($content_list, ['title' => $title_list[$count], 'content' => $sub_content_list]);

        $courses->structured_outline = $content_list;

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
            'data' => $content_list
        ], 200);
    }

    public function chapterQuizDetails(Request $request)
    {
        $chapter_quiz_id = $request->quiz_id ? $request->quiz_id : 0;

        if (!$chapter_quiz_id) {
            return response()->json([
                'status' => false,
                'message' => 'Please, attach Quiz ID',
                'data' => []
            ], 422);
        }

        if ($chapter_quiz_id) {
            $set = QuizQuestionSet::inRandomOrder()->first();
            $quiz_details = ChapterQuiz::where('chapter_quizzes.id', $chapter_quiz_id)
                ->select(
                    'chapter_quizzes.*',
                    'class_levels.name as class_name',
                    'subjects.name as subject_name',
                    'chapters.name as chapter_name',
                )
                ->leftJoin('class_levels', 'class_levels.id', 'chapter_quizzes.class_level_id')
                ->leftJoin('subjects', 'subjects.id', 'chapter_quizzes.subject_id')
                ->leftJoin('chapters', 'chapters.id', 'chapter_quizzes.chapter_id')
                ->first();

            $subject_list = ChapterQuizSubject::where('chapter_quiz_id', $chapter_quiz_id)->get();

            $quiz_details->written_question = ChapterQuizWrittenQuestion::where('chapter_quiz_id', $chapter_quiz_id)->first();
            
            $questions = [];
            foreach ($subject_list as $item) 
            {
                $set_question = ChapterQuizQuestion::inRandomOrder()
                ->where('chapter_quiz_id', $chapter_quiz_id)
                ->where('question_set_id', $set->id)
                ->where('chapter_quiz_subject_id', $item->id)
                ->limit($item->no_of_question)
                ->get();

                foreach ($set_question as $row) {
                    array_push($questions, $row);
                }
            }

            $quiz_details->questions = $questions;
        }

        return response()->json([
            'status' => true,
            'message' => 'Quiz Details',
            'data' => $quiz_details
        ], 200);
    }

    public function startQuiz(Request $request)
    {
        $user_id = $request->user()->id;
        $course_id = $request->course_id ? $request->course_id : 0;
        $chapter_quiz_id = $request->chapter_quiz_id ? $request->chapter_quiz_id : 0;

        if (!$course_id || !$chapter_quiz_id) {
            return response()->json([
                'status' => false,
                'message' => 'Please, attach ID',
                'data' => []
            ], 422);
        }

        $result = ChapterQuizResult::create([
            "user_id" => $user_id,
            "chapter_quiz_id" => $chapter_quiz_id,
            "course_id" => $course_id,
            "mark" => 0
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Result Details',
            'data' => $result
        ], 200);
    }

    public function submitQuizAnswer(Request $request)
    {
        $user_id = $request->user()->id;
        $result_id = $request->result_id ? $request->result_id : 0;
        $chapter_quiz_id = $request->chapter_quiz_id ? $request->chapter_quiz_id : 0;
        $answers = $request->answers ? $request->answers : [];

        if (empty($answers)) {
            return response()->json([
                'status' => false,
                'message' => 'Please, attach Answer!',
                'data' => []
            ], 422);
        }

        if (!$result_id) {
            return response()->json([
                'status' => false,
                'message' => 'Please, Start Exam properly!',
                'data' => []
            ], 422);
        }

        $positiveCount = 0;
        $negetiveCount = 0;

        $quiz_details = ChapterQuiz::where('id', $chapter_quiz_id)->first();

        if (empty($quiz_details)) {
            return response()->json([
                'status' => false,
                'message' => 'Quiz Not found!',
                'data' => []
            ], 422);
        }

        $subject_list = ChapterQuizSubject::where('chapter_quiz_id', $chapter_quiz_id)->get();

        foreach ($subject_list as $subject) {
            $subject->positive_count = 0;
            $subject->negetive_count = 0;
        }

        foreach ($answers as $ans) {
            $question = ChapterQuizQuestion::where('id', $ans['question_id'])->select(
                'id',
                'chapter_quiz_id',
                'question_set_id',
                'chapter_quiz_subject_id',
                'answer1',
                'answer2',
                'answer3',
                'answer4',
            )->first();

            $is_correct = false;

            $given_answer1 = $ans['answer1'] ? $ans['answer1'] : false;
            $given_answer2 = $ans['answer2'] ? $ans['answer2'] : false;
            $given_answer3 = $ans['answer3'] ? $ans['answer3'] : false;
            $given_answer4 = $ans['answer4'] ? $ans['answer4'] : false;

            if (
                $given_answer1 == $question->answer1
                && $given_answer2 == $question->answer2
                && $given_answer3 == $question->answer3
                && $given_answer4 == $question->answer4
            ) {
                $positiveCount++;
                $is_correct = true;

                foreach ($subject_list as $subject) {
                    if($subject->quiz_core_subject_id == $question->chapter_quiz_subject_id){
                        $subject->positive_count = $subject->positive_count + 1;
                    }
                }

            } else {
                $negetiveCount++;

                foreach ($subject_list as $subject) {
                    if($subject->quiz_core_subject_id == $question->chapter_quiz_subject_id){
                        $subject->negetive_count = $subject->negetive_count + 1;
                    }
                }
            }

            ChapterQuizResultAnswer::insert([
                'chapter_quiz_result_id' => $result_id,
                'question_id' => $ans['question_id'],
                'answer1' => $ans['answer1'] ? $ans['answer1'] : 0,
                'answer2' => $ans['answer2'] ? $ans['answer2'] : 0,
                'answer3' => $ans['answer3'] ? $ans['answer3'] : 0,
                'answer4' => $ans['answer4'] ? $ans['answer4'] : 0,
                'is_correct' => $is_correct
            ]);
        }

        // Subject Wise Result
        foreach ($subject_list as $subject) {
            ChapterQuizSubjectWiseResult::create([
                'chapter_quiz_result_id' => $result_id,
                'chapter_quiz_id' => $chapter_quiz_id,
                'user_id' => $user_id,
                'quiz_core_subject_id' => $subject->quiz_core_subject_id,
                'positive_count' => $subject->positive_count,
                'negetive_count' => $subject->negetive_count
            ]);
        }

        $mark = $positiveCount * $quiz_details->positive_mark - $negetiveCount * $quiz_details->negative_mark;

        ChapterQuizResult::where('id', $result_id)->update([
            "mark" => $mark,
            'positive_count' => $positiveCount,
            'negetive_count' => $negetiveCount,
            "submission_status" => "Submitted"
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Quiz Submitted Successful!',
            'data' => []
        ], 200);
    }

    public function submitWrittenAnswer(Request $request)
    {
        $user_id = $request->user()->id;

        $formData = json_decode($request->data, true);
        $result_id = $formData["result_id"] ? $formData["result_id"] : 0;
        $chapter_quiz_id = $formData["chapter_quiz_id"] ? $formData["chapter_quiz_id"] : 0;
        $attach_count = $formData["attach_count"] ? $formData["attach_count"] : 0;

        if (!$attach_count) {
            return response()->json([
                'status' => false,
                'message' => 'Please, attach Answer!',
                'data' => []
            ], 422);
        }

        if (!$result_id) {
            return response()->json([
                'status' => false,
                'message' => 'Please, Start Exam properly!',
                'data' => []
            ], 422);
        }

        if($attach_count){
            for($i = 0; $i < $attach_count; $i++){
                $attach_file = "attachment_".$i;
                $attachment_file = '';
                if($request->$attach_file){
                    $attachment_file  =  'written_answer_'. $chapter_quiz_id . "_" . $i . "_" .time().'.'.$request->$attach_file->getClientOriginalExtension();
                    $request->$attach_file->move('uploads/written_answer/', $attachment_file);
                }

                ChapterQuizWrittenAttachment::create([
                    'chapter_quiz_result_id' => $result_id,
                    'chapter_quiz_id' => $chapter_quiz_id,
                    'user_id' => $user_id,
                    'attachment_url' => "uploads/written_answer/".$attachment_file,
                ]);
            }

            $written = ChapterQuizWrittenQuestion::where('chapter_quiz_id', $chapter_quiz_id)->first();

            for ($i=1; $i <= $written->no_of_question; $i++) { 
                ChapterQuizWrittenMark::create([
                    'chapter_quiz_result_id' => $result_id,
                    'chapter_quiz_id' => $chapter_quiz_id,
                    'user_id' => $user_id,
                    'question_no' => $i, 
                    'mark' => 0.00, 
                    'marks_givenby_id' => 0
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Quiz Submitted Successful!',
            'data' => []
        ], 200);
    }

    public function submitWrittenAnswerMobile(Request $request)
    {
        $user_id = $request->user()->id;

        $formData = json_decode($request->data, true);
        $result_id = $formData["result_id"] ? $formData["result_id"] : 0;
        $chapter_quiz_id = $formData["chapter_quiz_id"] ? $formData["chapter_quiz_id"] : 0;
        $attach_count = $formData["attach_count"] ? $formData["attach_count"] : 0;

        if (!$attach_count) {
            return response()->json([
                'status' => false,
                'message' => 'Please, attach Answer!',
                'data' => []
            ], 422);
        }

        if (!$result_id) {
            return response()->json([
                'status' => false,
                'message' => 'Please, Start Exam properly!',
                'data' => []
            ], 422);
        }

        if($attach_count){
            $files = $request->file('files');
            $i = 1;

            foreach ($files as $file) {
                $attachment_file = '';
                if($file){
                    $attachment_file = 'wa_'. $chapter_quiz_id . "_" . $i . "_" .time().'.'.$file->getClientOriginalExtension();
                    $file->move('uploads/written_answer/', $attachment_file);
                }
    
                ChapterQuizWrittenAttachment::create([
                    'chapter_quiz_result_id' => $result_id,
                    'chapter_quiz_id' => $chapter_quiz_id,
                    'user_id' => $user_id,
                    'attachment_url' => "uploads/written_answer/".$attachment_file,
                ]);
                $i++;
            }

            $written = ChapterQuizWrittenQuestion::where('chapter_quiz_id', $chapter_quiz_id)->first();

            for ($i=1; $i <= $written->no_of_question; $i++) { 
                ChapterQuizWrittenMark::create([
                    'chapter_quiz_result_id' => $result_id,
                    'chapter_quiz_id' => $chapter_quiz_id,
                    'user_id' => $user_id,
                    'question_no' => $i, 
                    'mark' => 0.00, 
                    'marks_givenby_id' => 0
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Quiz Submitted Successful!',
            'data' => []
        ], 200);
    }

    public function quizAnswerList(Request $request)
    {
        $user_id = $request->user()->id;

        $answer_list = ChapterQuizResult::select(
            'chapter_quiz_results.*',
            'chapter_quizzes.title',
            'chapter_quizzes.title_bn',
            'chapter_quizzes.duration',
            'chapter_quizzes.positive_mark',
            'chapter_quizzes.negative_mark',
            'chapter_quizzes.total_mark as exam_mark',
            'chapter_quizzes.number_of_question',
            'class_levels.name as class_name',
            'subjects.name as subject_name',
            'chapters.name as chapter_name',
        )
            ->leftJoin('chapter_quizzes', 'chapter_quizzes.id', 'chapter_quiz_results.chapter_quiz_id')
            ->leftJoin('class_levels', 'class_levels.id', 'chapter_quizzes.class_level_id')
            ->leftJoin('subjects', 'subjects.id', 'chapter_quizzes.subject_id')
            ->leftJoin('chapters', 'chapters.id', 'chapter_quizzes.chapter_id')
            ->where('chapter_quiz_results.user_id', $user_id)
            ->orderBy('chapter_quiz_results.id', 'DESC')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Quiz List Successful!',
            'data' => $answer_list
        ], 200);
    }

    public function quizAnswerDetails(Request $request)
    {
        $user_id = $request->user()->id;
        $result_id = $request->result_id ? $request->result_id : 0;

        $answer = ChapterQuizResult::select(
            'chapter_quiz_results.*',
            'chapter_quizzes.title',
            'chapter_quizzes.title_bn',
            'chapter_quizzes.duration',
            'chapter_quizzes.positive_mark',
            'chapter_quizzes.negative_mark',
            'chapter_quizzes.total_mark as exam_mark',
            'chapter_quizzes.number_of_question',
            'class_levels.name as class_name',
            'subjects.name as subject_name',
            'chapters.name as chapter_name',
        )
            ->leftJoin('chapter_quizzes', 'chapter_quizzes.id', 'chapter_quiz_results.chapter_quiz_id')
            ->leftJoin('class_levels', 'class_levels.id', 'chapter_quizzes.class_level_id')
            ->leftJoin('subjects', 'subjects.id', 'chapter_quizzes.subject_id')
            ->leftJoin('chapters', 'chapters.id', 'chapter_quizzes.chapter_id')
            ->where('chapter_quiz_results.id', $result_id)
            ->orderBy('chapter_quiz_results.id', 'DESC')
            ->first();

        $answer->questions = ChapterQuizResultAnswer::select(
            'chapter_quiz_result_answers.*',
            'chapter_quiz_questions.question_text',
            'chapter_quiz_questions.question_text_bn',
            'chapter_quiz_questions.option1',
            'chapter_quiz_questions.option2',
            'chapter_quiz_questions.option3',
            'chapter_quiz_questions.option4',
            'chapter_quiz_questions.answer1 as correct_answer1',
            'chapter_quiz_questions.answer2 as correct_answer2',
            'chapter_quiz_questions.answer3 as correct_answer3',
            'chapter_quiz_questions.answer4 as correct_answer4',
        )
            ->leftJoin('chapter_quiz_questions', 'chapter_quiz_questions.id', 'chapter_quiz_result_answers.question_id')
            ->where('chapter_quiz_result_answers.chapter_quiz_result_id', $result_id)
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Answer Successful!',
            'data' => $answer
        ], 200);
    }

    public function quizSubjectWiseAnswerDetails(Request $request)
    {
        $result_id = $request->result_id ? $request->result_id : 0;

        $answer = ChapterQuizSubjectWiseResult::select(
            'chapter_quiz_subject_wise_results.*',
            'quiz_core_subjects.name',
            'quiz_core_subjects.name_bn'
        )
            ->leftJoin('quiz_core_subjects', 'quiz_core_subjects.id', 'chapter_quiz_subject_wise_results.quiz_core_subject_id')
            ->where('chapter_quiz_subject_wise_results.chapter_quiz_result_id', $result_id)
            ->orderBy('quiz_core_subjects.name', 'ASC')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Answer Details Successful!',
            'data' => $answer
        ], 200);
    }

    public function mentorStudentList(Request $request)
    {
        $user_id = $request->user()->id;
        $mentor = MentorInformation::where('user_id', $user_id)->first();

        $student = CourseStudentMapping::select(
            'course_student_mappings.student_id',
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

    public function mentorStudentListByCourse(Request $request)
    {
        $user_id = $request->user()->id;

        $course_id = $request->course_id ? $request->course_id : 0;

        if (!$course_id) {
            return response()->json([
                'status' => false,
                'message' => 'Please, attach ID',
                'data' => []
            ], 422);
        }

        $mentor = MentorInformation::where('user_id', $user_id)->first();

        $student = CourseStudentMapping::select(
            'course_student_mappings.student_id',
            'course_student_mappings.id as mapping_id',
            'courses.title as course_title',
            'mentor_informations.name as mentor_name',
            'student_informations.name as student_name',
            'student_informations.contact_no as student_contact_no'
        )
            ->where('course_student_mappings.mentor_id', $mentor->id)
            ->where('course_student_mappings.course_id', $course_id)
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
            ->orderBy('class_schedules.schedule_datetime', 'DESC')
            ->get();

        foreach ($class as $item) {

            if($item->start_time){
                $item->start_time = $this->addHour($item->start_time, 6);
            }

            if($item->end_time){
                $item->end_time = $this->addHour($item->end_time, 6);
            }

            $isToday = date('Ymd') == date('Ymd', strtotime($item->schedule_datetime));

            $zoomLink = MentorZoomLink::where('mentor_id', $item->mentor_id)->first();

            if ($isToday) {
                $item->can_join = true;
                $item->join_link = $zoomLink->live_link;
            } else {
                $item->can_join = false;
                $item->join_link = null;
            }

            $item->has_passed = false;
            if (time() > strtotime($item->schedule_datetime)) {
                $item->has_passed = true;
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Successful',
            'data' => $class
        ], 200);
    }

    public function addClassSchedule(Request $request)
    {
        $mapping_id = $request->mapping_id ? $request->mapping_id : 0;
        $schedule_date = $request->schedule_date ? $request->schedule_date : 0;

        if (!$mapping_id) {
            return response()->json([
                'status' => false,
                'message' => 'Please, attach Class ID',
                'data' => []
            ], 422);
        }

        $mapping_details = CourseStudentMapping::where('id', $mapping_id)->first();

        ClassSchedule::create([
            "course_student_mapping_id" => $mapping_id,
            "course_id" => $mapping_details->course_id,
            "student_id" => $mapping_details->student_id,
            "mentor_id" => $mapping_details->mentor_id,
            "schedule_datetime" => $schedule_date,
            "has_started" => false,
            "has_completed" => false,
            "start_time" => null,
            "end_time" => null,
            "is_active" => true
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Class added successful!',
            'data' => []
        ], 200);
    }

    public function updateClassSchedule(Request $request)
    {
        $schedule_id = $request->schedule_id ? $request->schedule_id : 0;
        $schedule_date = $request->schedule_date ? $request->schedule_date : 0;

        if (!$schedule_id) {
            return response()->json([
                'status' => false,
                'message' => 'Please, attach ID',
                'data' => []
            ], 422);
        }

        $schedule_details = ClassSchedule::where('id', $schedule_id)->first();

        $schedule_details->update([
            "schedule_datetime" => $schedule_date
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Class updated successful!',
            'data' => []
        ], 200);
    }

    public function startLiveClass(Request $request)
    {
        $schedule_id = $request->schedule_id ? $request->schedule_id : 0;

        if (!$schedule_id) {
            return response()->json([
                'status' => false,
                'message' => 'Please, attach ID',
                'data' => []
            ], 422);
        }

        $schedule_details = ClassSchedule::where('id', $schedule_id)->first();

        if ($schedule_details->has_started) {
            return response()->json([
                'status' => false,
                'message' => 'You can not start this class! Because it\'s already been started!',
                'data' => []
            ], 422);
        }

        $schedule_details->update([
            "start_time" => date("Y-m-d H:i:s"),
            "has_started" => true
        ]);

        return response()->json([
            'status' => true,
            'message' => 'The class has been started! Please take care of your student!',
            'data' => []
        ], 200);
    }

    public function studentJoinClass(Request $request)
    {
        $schedule_id = $request->schedule_id ? $request->schedule_id : 0;

        if (!$schedule_id) {
            return response()->json([
                'status' => false,
                'message' => 'Please, attach ID',
                'data' => []
            ], 422);
        }

        $schedule_details = ClassSchedule::where('id', $schedule_id)->first();

        if (!$schedule_details->has_started) {
            return response()->json([
                'status' => false,
                'message' => 'You can not join this class! Because this class has not been started yet!!',
                'data' => []
            ], 422);
        }

        StudentJoinHistory::create([
            'class_schedule_id' => $schedule_id,
            'student_id' => $schedule_details->student_id,
            'join_time' => date("Y-m-d H:i:s")
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Enjoy your class!!',
            'data' => []
        ], 200);
    }

    public function studentClassJoinHistory(Request $request)
    {
        $schedule_id = $request->schedule_id ? $request->schedule_id : 0;

        if (!$schedule_id) {
            return response()->json([
                'status' => false,
                'message' => 'Please, attach ID',
                'data' => []
            ], 422);
        }

        $schedule_details = ClassSchedule::select(
            'class_schedules.*',
            'courses.title as course_title',
            'mentor_informations.name as mentor_name'
        )
            ->leftJoin('courses', 'courses.id', 'class_schedules.course_id')
            ->leftJoin('mentor_informations', 'mentor_informations.id', 'class_schedules.mentor_id')
            ->where('class_schedules.id', $schedule_id)
            ->first();

        $history = StudentJoinHistory::where('class_schedule_id', $schedule_id)->get();
        foreach ($history as $item) {
            $item->join_time = $this->addHour($item->join_time, 6);
            $item->schedule_datetime = $schedule_details->schedule_datetime;
            $item->course_title = $schedule_details->course_title;
            $item->mentor_name = $schedule_details->mentor_name;
        }

        return response()->json([
            'status' => true,
            'message' => 'History List!',
            'data' => $history
        ], 200);
    }

    public function endLiveClass(Request $request)
    {
        $schedule_id = $request->schedule_id ? $request->schedule_id : 0;

        if (!$schedule_id) {
            return response()->json([
                'status' => false,
                'message' => 'Please, attach ID',
                'data' => []
            ], 422);
        }

        $schedule_details = ClassSchedule::where('id', $schedule_id)->first();

        if (!$schedule_details->has_started) {
            return response()->json([
                'status' => false,
                'message' => 'Please start class first! You can not end a class before starts!',
                'data' => []
            ], 422);
        }

        $schedule_details->update([
            "end_time" => date("Y-m-d H:i:s"),
            "has_completed" => true
        ]);

        return response()->json([
            'status' => true,
            'message' => 'The class has been ended! Thank You!',
            'data' => []
        ], 200);
    }

    public function studentEndLiveClass(Request $request)
    {
        $schedule_id = $request->schedule_id ? $request->schedule_id : 0;

        if (!$schedule_id) {
            return response()->json([
                'status' => false,
                'message' => 'Please, attach ID',
                'data' => []
            ], 422);
        }

        $schedule_details = ClassSchedule::where('id', $schedule_id)->first();

        if (!$schedule_details->has_started) {
            return response()->json([
                'status' => false,
                'message' => 'Please start class first! You can not end a class before starts!',
                'data' => []
            ], 422);
        }

        $schedule_details->update([
            "student_end_time" => date("Y-m-d H:i:s")
        ]);

        return response()->json([
            'status' => true,
            'message' => 'The class has been ended! Thank You!',
            'data' => []
        ], 200);
    }

    public function deleteClassSchedule(Request $request)
    {
        $schedule_id = $request->schedule_id ? $request->schedule_id : 0;

        if (!$schedule_id) {
            return response()->json([
                'status' => false,
                'message' => 'Please, attach ID',
                'data' => []
            ], 422);
        }

        $schedule_details = ClassSchedule::where('id', $schedule_id)->first();
        if ($schedule_details->has_started) {
            return response()->json([
                'status' => false,
                'message' => 'You cannot delete the class, because it\'s already been started!',
                'data' => []
            ], 422);
        }

        ClassSchedule::where('id', $schedule_id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Class deleted successful!',
            'data' => []
        ], 200);
    }

    public function mentorCompletedClassList(Request $request)
    {
        $user_id = $request->user()->id;
        $from = $request->start_date ? $request->start_date.' 00:00:00' : '';
        $to = $request->end_date ? $request->end_date.' 23:59:59' : '';;
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
            ->whereBetween('schedule_datetime', [$from, $to])
            ->leftJoin('courses', 'courses.id', 'class_schedules.course_id')
            ->leftJoin('mentor_informations', 'mentor_informations.id', 'class_schedules.mentor_id')
            ->leftJoin('student_informations', 'student_informations.id', 'class_schedules.student_id')
            ->get();
        
        $times = [];
        foreach ($class as $key => $item) {
            $item->start_time_gmt = $this->addHour($item->start_time, 6);
            $item->end_time_gmt = $this->addHour($item->end_time, 6);
            $item->total_minutes = $this->getTimeDifference($item->start_time, $item->end_time);
            array_push($times, $this->getTimeDifference($item->start_time, $item->end_time));
        }

        $response = [
            "total_time" => $this->calculateTime($times),
            "list" => $class
        ];

        return response()->json([
            'status' => true,
            'message' => 'Successful',
            'data' => $response
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

        foreach ($class as $item) {

            if($item->start_time){
                $item->start_time = $this->addHour($item->start_time, 6);
            }

            if($item->end_time){
                $item->end_time = $this->addHour($item->end_time, 6);
            }

            $isToday = date('Ymd') == date('Ymd', strtotime($item->schedule_datetime));

            $zoomLink = MentorZoomLink::where('mentor_id', $item->mentor_id)->first();

            if (!empty($zoomLink)) {
                $item->join_link = $zoomLink->live_link;
            } else {
                $item->join_link = null;
            }

            if ($isToday) {
                $item->can_join = true;
                $item->join_link = $zoomLink->live_link;
            } else {
                $item->can_join = false;
                $item->join_link = null;
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

            if($item->start_time){
                $item->start_time = $this->addHour($item->start_time, 6);
            }

            if($item->end_time){
                $item->end_time = $this->addHour($item->end_time, 6);
            }

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
                //array_push($class_list, $item);
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
                'course_type_id' => $request->course_type_id,
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
        $studentList = StudentInformation
            ::select(
                'student_informations.id',
                'student_informations.name',
                'student_informations.user_id',
                'student_informations.email',
                'student_informations.username',
                'student_informations.contact_no',
            )->latest()->get();
        return $this->apiResponse($studentList, 'Student List', true, 200);
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
            DB::beginTransaction();
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
                DB::commit();
                return $this->apiResponse([], 'Course mentor Created Successfully', true, 201);
            } else {
                $mentor = CourseMentor::where('id', $request->id)->first();
                $mentor->update($request->all());
                return $this->apiResponse([], 'Course mentor Updated Successfully', true, 200);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
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
            DB::beginTransaction();
            if (empty($request->id)) {
                $mapping = json_decode($request->mapping, true);
                if ($mapping) {
                    $studentMapping = [];
                    foreach ($mapping as $key => $value) {
                        $studentMapping[] = [
                            'course_id' => $value['course_id'],
                            'mentor_id' => $value['mentor_id'],
                            'student_id' => $value['student_id'],
                            'is_active' => $value['is_active'],
             
                        ];
                    }
                    CourseStudentMapping::insert($studentMapping);
                }
                DB::commit();
                return $this->apiResponse([], 'Course studentMapping Created Successfully', true, 201);
            } else {

                $studentMapping = CourseStudentMapping::where('id', $request->id)->first();
                $studentMapping->update([
                    'is_active' => $request->is_active,
                ]);

                return $this->apiResponse([], 'Course studentMapping Updated Successfully', true, 200);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
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

    public function mentorListByCourse(Request $request, $id)
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

    public function courseStudentMappingDelete(Request $request, $id)
    {
        CourseStudentMapping::where('id', $id)->delete();
        return $this->apiResponse([], 'Course Student Mapping Deleted Successfully', true, 200);
    }

    public function courseTypeList(Request $request)
    {
        $courseType = CourseType::select(
            'course_types.id',
            'course_types.name',
            'course_types.name_bn',
            'course_types.is_active',
        )->latest()->get();
        return $this->apiResponse($courseType, 'Course Type List', true, 200);
    }


    public function enrollMentorList(Request $request)
    {
        $id = $request->id?$request->id:0;
        $mentorList = CourseParticipant:: where('item_type', "Course")
        ->leftJoin('student_informations', 'student_informations.user_id', 'course_participants.user_id')
        ->leftJoin('courses', 'courses.id', 'course_participants.item_id')
            ->select(
                'course_participants.*',
                'student_informations.name as student_name',
                'student_informations.email as student_email',
                'courses.title as course_title',

            )->when($id, function ($query, $id) {
                return $query->where('course_participants.item_id', $id);
            })->latest()->get();

       

        return $this->apiResponse($mentorList, 'Mentor List', true, 200);
    }

    public function courseFreeEnrollment(Request $request)
    {
        try {
            DB::beginTransaction();
            $item = Course::where('id', $request->item_id)->first();
            foreach($request->user_id as $value ){
                $alreadyEnroll = CourseParticipant::where('item_id', $request->item_id)
                ->where('user_id', $value['user_id'])
                ->where('item_type', "Course")
                ->first();

                if ($alreadyEnroll) {
                    continue;
                }

                $payment = Payment::create([
                    'user_id' => $value['user_id'],
                    'item_id' => $request->item_id,
                    'item_type' => "Course",
                    'is_promo_applied' => true,
                    'promo_id' => $request->promo_id,
                    'payable_amount' => $item->sale_price,
                    'paid_amount' => 0.00,
                    'discount_amount' => $item->sale_price,
                    'currency' => 'BDT',
                    'transaction_id' => uniqid(),
                    'payment_type' => 'BACBON',
                    'payment_method' => 'BACBON',
                    'status' => 'Completed',
                ]);

                PaymentDetail::create([
                    'payment_id' => $payment->id,
                    'user_id' => $value['user_id'],
                    'item_id' => $request->item_id,
                    'unit_price' => $item->sale_price,
                    'quantity' => 1,
                    'total' => $item->sale_price,
                ]);

                CourseParticipant::create([
                    'item_id' => $request->item_id,
                    'user_id' => $value['user_id'],
                    'item_type' => "course",
                    'payment_id' => $payment->id,
                    'item_price' => $item->sale_price,
                    'paid_amount' => 0.00,
                    'discount' => $item->sale_price,
                    'item_type' => 'Course',
                    'is_trial_taken' => false,
                    'is_active' => $request->is_active,
                ]);
            }
            DB::commit();
            return $this->apiResponse([], 'Course Free Enrollment Updated Successfully', true, 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }

    // 

    public function adminCompletedClassList(Request $request)
    {
        $course_id = $request->course_id ? $request->course_id : 0;
        $mentor_id = $request->mentor_id ? $request->mentor_id : 0;
        $student_id = $request->student_id ? $request->student_id : 0;
        $from = $request->from ? $request->from.' 00:00:00' : '';
        $to = $request->to ? $request->to.' 23:59:59' : '';


        $class = ClassSchedule::select(
            'class_schedules.*',
            'courses.title as course_title',
            'mentor_informations.name as mentor_name',
            'student_informations.name as student_name',
            'student_informations.contact_no as student_contact_no'
        )
        
            ->leftJoin('courses', 'courses.id', 'class_schedules.course_id')
            ->leftJoin('mentor_informations', 'mentor_informations.id', 'class_schedules.mentor_id')
            ->leftJoin('student_informations', 'student_informations.id', 'class_schedules.student_id')
            ->where('class_schedules.course_id', $course_id)
            ->where('class_schedules.mentor_id', $mentor_id)
            ->where('class_schedules.has_completed', true)
            ->whereBetween('schedule_datetime', [$from, $to])
            ->when($student_id, function ($query, $student_id) {
                return $query->where('class_schedules.student_id', $student_id);
            })
            ->get();

            
            // return $class;
        // $class = ClassSchedule::select(
        //     'class_schedules.*',
        //     'courses.title as course_title',
        //     'mentor_informations.name as mentor_name',
        //     'student_informations.name as student_name',
        //     'student_informations.contact_no as student_contact_no'
        // )
        //     ->where('class_schedules.course_id', $course_id)
        //     ->where('class_schedules.mentor_id', $mentor_id)
        //     ->where('class_schedules.student_id', $student_id)
        //     ->where('class_schedules.has_completed', true)
        //     ->whereBetween('schedule_datetime', [$from, $to])
        //     ->leftJoin('courses', 'courses.id', 'class_schedules.course_id')
        //     ->leftJoin('mentor_informations', 'mentor_informations.id', 'class_schedules.mentor_id')
        //     ->leftJoin('student_informations', 'student_informations.id', 'class_schedules.student_id')
        //     ->get();
        
        $times = [];
        foreach ($class as $key => $item) {
            $item->start_time_gmt = $this->addHour($item->start_time, 6);
            $item->end_time_gmt = $this->addHour($item->end_time, 6);
            $item->total_minutes = $this->getTimeDifference($item->start_time, $item->end_time);
            array_push($times, $this->getTimeDifference($item->start_time, $item->end_time));
        }

        $response = [
            "total_time" => $this->calculateTime($times),
            "list" => $class
        ];

        return $this->apiResponse($response, 'Successful', true, 200);
    }

   
}
