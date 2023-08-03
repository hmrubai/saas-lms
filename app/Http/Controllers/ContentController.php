<?php

namespace App\Http\Controllers;

use App\Http\Traits\HelperTrait;
use App\Models\Category;
use App\Models\Content;
use App\Models\Chapter;
use App\Models\ChapterQuiz;
use App\Models\ContentOutline;
use App\Models\ChapterQuizQuestion;
use App\Models\ChapterScript;
use App\Models\ChapterVideo;
use App\Models\ClassLevel;
use App\Models\QuizQuestionSet;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContentController extends Controller
{
    use HelperTrait;

    public function questionSetList()
    {
        $setList = QuizQuestionSet::select('id', 'name')->get();
        return $this->apiResponse($setList, 'Set List Successful', true, 200);
    }

    public function subjectListByClassID(Request $request)
    {
        $class_id = $request->class_id;
        $subjectList = Subject::select('id', 'name', 'name_bn', 'class_level_id')->where('class_level_id', $class_id)->get();
        return $this->apiResponse($subjectList, 'Subject List Successful', true, 200);
    }

    public function chapterListBySubjectID(Request $request)
    {
        $subject_id = $request->subject_id;
        $subjectList = Chapter::select('id', 'name', 'name_bn', 'subject_id')->where('subject_id', $subject_id)->get();
        return $this->apiResponse($subjectList, 'Chapter List Successful', true, 200);
    }

    public function scriptListByChapterID(Request $request)
    {
        $chapter_id = $request->chapter_id;
        $scriptList = ChapterScript::select('id', 'title', 'title_bn', 'chapter_id')->where('chapter_id', $chapter_id)->get();
        return $this->apiResponse($scriptList, 'Script List Successful', true, 200);
    }

    public function videoListByChapterID(Request $request)
    {
        $chapter_id = $request->chapter_id;
        $videoList = ChapterVideo::select('id', 'title', 'title_bn', 'chapter_id')->where('chapter_id', $chapter_id)->get();
        return $this->apiResponse($videoList, 'Video List Successful', true, 200);
    }

    public function quizListByChapterID(Request $request)
    {
        $chapter_id = $request->chapter_id;
        $quizList = ChapterQuiz::select('id', 'title', 'title_bn', 'chapter_id')->where('chapter_id', $chapter_id)->get();
        return $this->apiResponse($quizList, 'Quiz List Successful', true, 200);
    }







    public function classList()
    {
        $classList = ClassLevel::select('id', 'name', 'name_bn', 'class_code', 'price', 'is_free', 'icon', 'color_code', 'sequence', 'is_active')->get();
        return $this->apiResponse($classList, 'Class List Successful', true, 200);
    }

    public function saveOrUpdateClass(Request $request)
    {
        try {
            $classLevel = [
                'name' => $request->name,
                'name_bn' => $request->name_bn,
                'price' => $request->price,
                'is_free' => $request->is_free,
                'color_code' => $request->color_code,
                'sequence' => $request->sequence,
                'is_active' => $request->is_active,
            ];

            if (empty($request->id)) {
                $classList = ClassLevel::create($classLevel);
                $classList->update([
                    'class_code' => $this->codeGenerator('CC', ClassLevel::class),
                ]);

                if ($request->hasFile('icon')) {
                    $classList->update([
                        'icon' => $this->imageUpload($request, 'icon', 'icon'),
                    ]);
                }
                return $this->apiResponse([], 'Class Created Successfully', true, 201);
            } else {

                $class = ClassLevel::where('id', $request->id)->first();
                if ($request->hasFile('icon')) {
                    ClassLevel::where('id', $request->id)->update([
                        'icon' => $this->imageUpload($request, 'icon', 'icon', $class->icon)
                    ]);
                }

                $class->update($classLevel);
                return $this->apiResponse([], 'Class Updated Successfully', true, 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => []
            ], 200);
        }
    }

    public function subjectList()
    {
        $subjectList = Subject::leftJoin('class_levels', 'class_levels.id', '=', 'subjects.class_level_id')->select(
            'subjects.id',
            'subjects.name',
            'subjects.name_bn',
            'subjects.class_level_id',
            'subjects.subject_code',
            'subjects.price',
            'subjects.is_free',
            'subjects.icon',
            'subjects.color_code',
            'subjects.sequence',
            'subjects.is_active',
            'class_levels.name as class_name',
            'class_levels.name_bn as class_name_bn'
        )

            ->get();
        return $this->apiResponse($subjectList, 'Subject List Successful', true, 200);
    }

    public function saveOrUpdateSubject(Request $request)
    {
        try {
            $subjects = [
                "name" => $request->name,
                "name_bn" => $request->name_bn,
                "class_level_id" => $request->class_level_id,
                "subject_code" => $this->codeGenerator('SC', Subject::class),
                "price" => $request->price,
                "is_free" => $request->is_free,
                "color_code" => $request->color_code,
                "sequence" => $request->sequence,
                "is_active" => $request->is_active,
            ];

            if (empty($request->id)) {
                $subjectList = Subject::create($subjects);
                $subjectList->update([
                    "subject_code" => $this->codeGenerator('SC', Subject::class),
                ]);

                if ($request->hasFile('icon')) {
                    $subjectList->update([
                        'icon' => $this->imageUpload($request, 'icon', 'icon'),
                    ]);
                }
                return $this->apiResponse([], 'Subject Created Successfully', true, 201);
            } else {
                $subject = Subject::where('id', $request->id)->first();
                if ($request->hasFile('icon')) {
                    Subject::where('id', $request->id)->update([
                        'icon' => $this->imageUpload($request, 'icon', 'icon', $subject->icon)
                    ]);
                }
                $subject->update($subjects);
                return $this->apiResponse([], 'Subject Updated Successfully', true, 200);
            }
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }

    public function chapterList()
    {
        $chapterList = Chapter::leftJoin('subjects', 'subjects.id', '=', 'chapters.subject_id')
            ->select(
                'chapters.id',
                'chapters.name',
                'chapters.name_bn',
                'chapters.subject_id',
                'chapters.chapter_code',
                'chapters.price',
                'chapters.is_free',
                'chapters.icon',
                'chapters.color_code',
                'chapters.sequence',
                'chapters.is_active',
                'subjects.name as subject_name',
                'subjects.name_bn as subject_name_bn'
            )
            ->get();
        return $this->apiResponse($chapterList, 'Chapter List Successful', true, 200);
    }

    public function saveOrUpdateChapter(Request $request)
    {
        try {
            $chapter = [
                "name" => $request->name,
                "name_bn" => $request->name_bn,
                "class_level_id" => $request->class_level_id,
                "subject_id" => $request->subject_id,
                "price" => $request->price,
                "is_free" => $request->is_free,
                "color_code" => $request->color_code,
                "sequence" => $request->sequence,
                "is_active" => $request->is_active,
            ];

            if (empty($request->id)) {
                $chapterList = Chapter::create($chapter);
                $chapterList->update([
                    "chapter_code" => $this->codeGenerator('CHC', Chapter::class),
                ]);
                if ($request->hasFile('icon')) {
                    $chapterList->update([
                        'icon' => $this->imageUpload($request, 'icon', 'icon'),
                    ]);
                }
                return $this->apiResponse([], 'Chapter Created Successfully', true, 201);
            } else {
                $chapter = Chapter::where('id', $request->id)->first();

                if ($request->hasFile('icon')) {
                    Chapter::where('id', $request->id)->update([
                        'icon' => $this->imageUpload($request, 'icon', 'icon', $chapter->icon)
                    ]);
                }
                $chapter->update([
                    "name" => $request->name,
                    "name_bn" => $request->name_bn,
                    "class_level_id" => $request->class_level_id,
                    "subject_id" => $request->subject_id,
                    "chapter_code" => $this->codeGenerator('CHC', Chapter::class),
                    "price" => $request->price,
                    "is_free" => $request->is_free,
                    "color_code" => $request->color_code,
                    "sequence" => $request->sequence,
                    "is_active" => $request->is_active,
                ]);
                return $this->apiResponse([], 'Chapter Updated Successfully', true, 200);
            }
        } catch (\Throwable $th) {

            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }

    public function videoChapterList()
    {
        $videoChapterList = ChapterVideo::leftJoin('class_levels', 'class_levels.id', '=', 'chapter_videos.class_level_id')
            ->leftJoin('subjects', 'subjects.id', '=', 'chapter_videos.subject_id')
            ->leftJoin('chapters', 'chapters.id', '=', 'chapter_videos.chapter_id')
            ->select(
                'chapter_videos.id',
                'chapter_videos.title',
                'chapter_videos.title_bn',
                'chapter_videos.class_level_id',
                'chapter_videos.subject_id',
                'chapter_videos.chapter_id',
                'chapter_videos.video_code',
                'chapter_videos.author_name',
                'chapter_videos.author_details',
                'chapter_videos.description',
                'chapter_videos.raw_url',
                'chapter_videos.s3_url',
                'chapter_videos.youtube_url',
                'chapter_videos.download_url',
                'chapter_videos.duration',
                'chapter_videos.price',
                'chapter_videos.rating',
                'chapter_videos.is_free',
                'chapter_videos.sequence',
                'chapter_videos.is_active',
                'class_levels.name as class_name',
                'class_levels.name_bn as class_name_bn',
                'subjects.name as subject_name',
                'subjects.name_bn as subject_name_bn',
                'chapters.name as chapter_name',
                'chapters.name_bn as chapter_name_bn',
                'chapter_videos.thumbnail'
            )
            ->get();
        return $this->apiResponse($videoChapterList, 'Video Chapter List Successful', true, 200);
    }

    public function saveOrUpdateChapterVideo(Request $request)
    {
        try {
            $chapters = [
                "title" => $request->title,
                "title_bn" => $request->title_bn,
                "class_level_id" => $request->class_level_id,
                "subject_id" => $request->subject_id,
                "chapter_id" => $request->chapter_id,
                "author_name" => $request->author_name,
                "author_details" => $request->author_details,
                "description" => $request->description,
                "raw_url" => $request->raw_url,
                "s3_url" => $request->s3_url,
                "youtube_url" => $request->youtube_url,
                "download_url" => $request->download_url,
                "duration" => $request->duration,
                "price" => $request->price,
                "rating" => $request->rating,
                "is_free" => $request->is_free,
                "sequence" => $request->sequence,
                "is_active" => $request->is_active,
            ];

            if (empty($request->id)) {
                $chapterList = ChapterVideo::create($chapters);
                $chapterList->update([
                    "video_code" => $this->codeGenerator('CVC', Chapter::class),
                ]);
                if ($request->hasFile('thumbnail')) {
                    $chapterList->update([
                        'thumbnail' => $this->imageUpload($request, 'thumbnail', 'thumbnail'),
                    ]);
                }
                return $this->apiResponse([], 'Chapter Video Created Successfully', true, 201);
            } else {

                $video = ChapterVideo::where('id', $request->id)->first();
                if ($request->hasFile('thumbnail')) {
                    ChapterVideo::where('id', $request->id)->update([
                        'thumbnail' => $this->imageUpload($request, 'thumbnail', 'thumbnail', $video->thumbnail)
                    ]);
                }
                $video->update($chapters);

                return $this->apiResponse([], 'Chapter Video Updated Successfully', true, 200);
            }
        } catch (\Throwable $th) {

            return  $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }

    public function saveOrUpdateScript(Request $request)
    {
        try {
            $scripts = [
                "title" => $request->title,
                "title_bn" => $request->title_bn,
                "description" => $request->description,
                "class_level_id" => $request->class_level_id,
                "subject_id" => $request->subject_id,
                "chapter_id" => $request->chapter_id,
                "raw_url" => $request->raw_url,
                "price" => $request->price,
                "rating" => $request->rating,
                "is_free" => $request->is_free,
                "sequence" => $request->sequence,
                "is_active" => $request->is_active,
            ];

            if (empty($request->id)) {
                $script = ChapterScript::create($scripts);
                $script->update([
                    "script_code" => $this->codeGenerator('CSC', ChapterScript::class),
                ]);
                if ($request->hasFile('thumbnail')) {
                    $script->update([
                        'thumbnail' => $this->imageUpload($request, 'thumbnail', 'thumbnail'),
                    ]);
                }
                return $this->apiResponse([], 'Chapter Script Created Successfully', true, 201);
            } else {
                $script = ChapterScript::where('id', $request->id)->first();
                if ($request->hasFile('thumbnail')) {
                    ChapterScript::where('id', $request->id)->update([
                        'thumbnail' => $this->imageUpload($request, 'thumbnail', 'thumbnail', $script->thumbnail)
                    ]);
                }
                $script->update($scripts);
                return $this->apiResponse([], 'Chapter Script Updated Successfully', true, 200);
            }
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }

    public function scriptChapterList()
    {
        $scriptChapterList = ChapterScript::leftJoin('class_levels', 'class_levels.id', '=', 'chapter_scripts.class_level_id')
            ->leftJoin('subjects', 'subjects.id', '=', 'chapter_scripts.subject_id')
            ->leftJoin('chapters', 'chapters.id', '=', 'chapter_scripts.chapter_id')
            ->select(
                'chapter_scripts.id',
                'chapter_scripts.title',
                'chapter_scripts.title_bn',
                'chapter_scripts.class_level_id',
                'chapter_scripts.subject_id',
                'chapter_scripts.chapter_id',
                'chapter_scripts.script_code',
                'chapter_scripts.description',
                'chapter_scripts.raw_url',
                'chapter_scripts.price',
                'chapter_scripts.rating',
                'chapter_scripts.is_free',
                'chapter_scripts.sequence',
                'chapter_scripts.is_active',
                'class_levels.name as class_name',
                'class_levels.name_bn as class_name_bn',
                'subjects.name as subject_name',
                'subjects.name_bn as subject_name_bn',
                'chapters.name as chapter_name',
                'chapters.name_bn as chapter_name_bn',
                'chapter_scripts.thumbnail'
            )
            ->get();
        return $this->apiResponse($scriptChapterList, 'Script Chapter List Successful', true, 200);
    }

    public function saveOrUpdateQuiz(Request $request)
    {
        try {
            $quizs = [
                "title" => $request->title,
                "title_bn" => $request->title_bn,
                "description" => $request->description,
                "class_level_id" => $request->class_level_id,
                "subject_id" => $request->subject_id,
                "chapter_id" => $request->chapter_id,
                "duration" => $request->duration,
                "positive_mark" => $request->positive_mark,
                "negative_mark" => $request->negative_mark,
                "total_mark" => $request->total_mark,
                "number_of_question" => $request->number_of_question,
                "is_free" => $request->is_free,
                "sequence" => $request->sequence,
                "is_active" => $request->is_active,
            ];
            if (empty($request->id)) {
                $quiz = ChapterQuiz::create($quizs);
                $quiz->update([
                    "quiz_code" => $this->codeGenerator('CQC', ChapterQuiz::class),
                ]);
                return $this->apiResponse([], 'Chapter Quiz Created Successfully', true, 201);
            } else {
                $quiz = ChapterQuiz::where('id', $request->id)->first();
                $quiz->update($quizs);
                return $this->apiResponse([], 'Chapter Quiz Updated Successfully', true, 200);
            }
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }

    public function chapterQuizList()
    {
        $chapterQuizList = ChapterQuiz::leftJoin('class_levels', 'class_levels.id', '=', 'chapter_quizzes.class_level_id')
            ->leftJoin('subjects', 'subjects.id', '=', 'chapter_quizzes.subject_id')
            ->leftJoin('chapters', 'chapters.id', '=', 'chapter_quizzes.chapter_id')
            ->select(
                'chapter_quizzes.id',
                'chapter_quizzes.title',
                'chapter_quizzes.title_bn',
                'chapter_quizzes.class_level_id',
                'chapter_quizzes.subject_id',
                'chapter_quizzes.chapter_id',
                'chapter_quizzes.quiz_code',
                'chapter_quizzes.description',
                'chapter_quizzes.duration',
                'chapter_quizzes.positive_mark',
                'chapter_quizzes.negative_mark',
                'chapter_quizzes.total_mark',
                'chapter_quizzes.number_of_question',
                'chapter_quizzes.is_free',
                'chapter_quizzes.sequence',
                'chapter_quizzes.is_active',
                'class_levels.name as class_name',
                'class_levels.name_bn as class_name_bn',
                'subjects.name as subject_name',
                'subjects.name_bn as subject_name_bn',
                'chapters.name as chapter_name',
                'chapters.name_bn as chapter_name_bn',
            )
            ->get();
        return $this->apiResponse($chapterQuizList, 'Chapter Quiz List Successful', true, 200);
    }

    public function saveOrUpdateQuizQuestion(Request $request)
    {
        try {
            $quizQuestions = [
                "chapter_quiz_id" => $request->chapter_quiz_id,
                "class_level_id" => $request->class_level_id,
                "subject_id" => $request->subject_id,
                "chapter_id" => $request->chapter_id,
                "question_text" => $request->question_text,
                "question_text_bn" => $request->question_text_bn,
                "question_set_id" => $request->question_set_id,
                "option1" => $request->option1,
                "option2" => $request->option2,
                "option3" => $request->option3,
                "option4" => $request->option4,
                "answer1" => $request->answer1,
                "answer2" => $request->answer2,
                "answer3" => $request->answer3,
                "answer4" => $request->answer4,
                "explanation_text" => $request->explanation_text,
                "is_active" => $request->is_active,
            ];

            if (empty($request->id)) {
                $quizQuestion = ChapterQuizQuestion::create($quizQuestions);
                if ($request->hasFile('question_image') || $request->hasFile('option1_image') || $request->hasFile('option2_image') || $request->hasFile('option3_image') || $request->hasFile('option4_image') || $request->hasFile('explanation_image')) {
                    $quizQuestion->update([
                        "question_image" => $this->imageUploadWithPrefix($request, 'question_image', 'quiz', 'question_image'),
                        "option1_image" => $this->imageUploadWithPrefix($request, 'option1_image', 'quiz', 'option1_image'),
                        "option2_image" => $this->imageUploadWithPrefix($request, 'option2_image', 'quiz', 'option2_image'),
                        "option3_image" => $this->imageUploadWithPrefix($request, 'option3_image', 'quiz', 'option3_image'),
                        "option4_image" => $this->imageUploadWithPrefix($request, 'option4_image', 'quiz', 'option4_image'),
                        "explanation_image" => $this->imageUploadWithPrefix($request, 'explanation_image', 'quiz', 'explanation_image'),
                    ]);
                }
                return $this->apiResponse([], 'Chapter Quiz Question Created Successfully', true, 201);
            } else {
                $quizQuestion = ChapterQuizQuestion::where('id', $request->id)->first();
                if (
                    $request->hasFile('question_image') ||
                    $request->hasFile('option1_image') ||
                    $request->hasFile('option2_image') ||
                    $request->hasFile('option3_image') ||
                    $request->hasFile('option4_image') ||
                    $request->hasFile('explanation_image')
                ) {
                    ChapterQuizQuestion::where('id', $request->id)->update([
                        "question_image" => $this->imageUploadWithPrefix($request, 'question_image', 'quiz', 'question_image', $quizQuestion->question_image),
                        "option1_image" => $this->imageUploadWithPrefix($request, 'option1_image', 'quiz', 'option1_image', $quizQuestion->option1_image),
                        "option2_image" => $this->imageUploadWithPrefix($request, 'option2_image', 'quiz', 'option2_image', $quizQuestion->option2_image),
                        "option3_image" => $this->imageUploadWithPrefix($request, 'option3_image', 'quiz', 'option3_image', $quizQuestion->option3_image),
                        "option4_image" => $this->imageUploadWithPrefix($request, 'option4_image', 'quiz', 'option4_image', $quizQuestion->option4_image),
                        "explanation_image" => $this->imageUploadWithPrefix($request, 'explanation_image', 'quiz', 'explanation_image', $quizQuestion->explanation_image),
                    ]);
                }

                $quizQuestion->update($quizQuestions);
                return $this->apiResponse([], 'Chapter Quiz Question Updated Successfully', true, 200);
            }
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }

    public function excelQuestionUpload(Request $request)
    {
        try {
            DB::beginTransaction();
            $excel_data = json_decode($request->excel_data, true);
            if ($excel_data) {
                $qtn = [];
                foreach ($excel_data as $key => $value) {
                    $qtn[] = [
                        'chapter_quiz_id' => $value['chapter_quiz_id'],
                        'class_level_id' => $value['class_level_id'],
                        'subject_id' => $value['subject_id'],
                        'chapter_id' => $value['chapter_id'],
                        'question_text' => $value['question_text'],
                        'question_text_bn' => $value['question_text_bn'],
                        'question_set_id' => $value['question_set_id'],
                        'option1' => $value['option1'],
                        'option2' => $value['option2'],
                        'option3' => $value['option3'],
                        'option4' => $value['option4'],
                        'answer1' => $value['answer1'],
                        'answer2' => $value['answer2'],
                        'answer3' => $value['answer3'],
                        'answer4' => $value['answer4'],
                        'explanation_text' => $value['explanation_text'],
                    ];
                }
                ChapterQuizQuestion::insert($qtn);
            }
            DB::commit();
            return $this->apiResponse($excel_data, 'Chapter Quiz Question Updated Successfully', true, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }

    public function quizQuestionList(Request $request)
    {
        $quiz_id = $request->id;
        $quizQuestions =
            ChapterQuizQuestion::where('chapter_quiz_id', $quiz_id)
            ->leftJoin('class_levels', 'class_levels.id', '=', 'chapter_quiz_questions.class_level_id')
            ->leftJoin('subjects', 'subjects.id', '=', 'chapter_quiz_questions.subject_id')
            ->leftJoin('chapters', 'chapters.id', '=', 'chapter_quiz_questions.chapter_id')
            ->leftJoin('chapter_quizzes', 'chapter_quizzes.id', '=', 'chapter_quiz_questions.chapter_quiz_id')
            ->select(
                'chapter_quiz_questions.id',
                'chapter_quiz_questions.chapter_quiz_id',
                'chapter_quiz_questions.class_level_id',
                'chapter_quiz_questions.subject_id',
                'chapter_quiz_questions.question_set_id',
                'chapter_quiz_questions.chapter_id',
                'chapter_quiz_questions.question_text',
                'chapter_quiz_questions.question_text_bn',
                'chapter_quiz_questions.option1',
                'chapter_quiz_questions.option2',
                'chapter_quiz_questions.option3',
                'chapter_quiz_questions.option4',
                'chapter_quiz_questions.answer1',
                'chapter_quiz_questions.answer2',
                'chapter_quiz_questions.answer3',
                'chapter_quiz_questions.answer4',
                'chapter_quiz_questions.explanation_text',
                'chapter_quiz_questions.is_active',
                'chapter_quiz_questions.question_image',
                'chapter_quiz_questions.option1_image',
                'chapter_quiz_questions.option2_image',
                'chapter_quiz_questions.option3_image',
                'chapter_quiz_questions.option4_image',
                'chapter_quiz_questions.explanation_image',
                'class_levels.name as class_name',
                'class_levels.name_bn as class_name_bn',
                'subjects.name as subject_name',
                'subjects.name_bn as subject_name_bn',
                'chapters.name as chapter_name',
                'chapters.name_bn as chapter_name_bn',
                'chapter_quizzes.title as quiz_title',
                'chapter_quizzes.title_bn as quiz_title_bn',
            )
            ->get();
        return $this->apiResponse($quizQuestions, 'Chapter Quiz Question List Successful', true, 200);
    }

    public function deleteQuestion(Request $request)
    {
        try {
            $question = ChapterQuizQuestion::where('id', $request->id)->first();

            if ($question->question_image != null) {
                $this->deleteImage($question->question_image);
            }
            if ($question->option1_image != null) {
                $this->deleteImage($question->option1_image);
            }
            if ($question->option2_image != null) {
                $this->deleteImage($question->option2_image);
            }
            if ($question->option3_image != null) {
                $this->deleteImage($question->option3_image);
            }
            if ($question->option4_image != null) {
                $this->deleteImage($question->option4_image);
            }
            if ($question->explanation_image != null) {
                $this->deleteImage($question->explanation_image);
            }
            $question->delete();

            return $this->apiResponse([], 'Question Deleted Successfully', true, 200);
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }

    public function allContentList(Request $request)
    {
        $menus = Category::where('is_content', true)->get();
        foreach ($menus as $item) {
            if ($item->is_content) {
                $content_list = Content::where('category_id', $item->id)->get();
                $item->contents = $content_list;

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
        }

        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' => $menus
        ], 200);
    }

    public function saveOrUpdateContent(Request $request)
    {
        try {
            $content = [
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
                'is_active' => $request->is_active,
                'is_free'   => $request->is_free,
                'sequence'  => $request->sequence,
                'appeared_from' => $request->appeared_from,
                'appeared_to'  => $request->appeared_to,
            ];

            if (empty($request->id)) {
                $courseList = Content::create($content);
                if ($request->hasFile('icon')) {
                    $courseList->update([
                        'icon' => $this->imageUpload($request, 'icon', 'icon'),
                        'thumbnail' => $this->imageUpload($request, 'thumbnail', 'thumbnail'),
                    ]);
                }
                return $this->apiResponse([], 'Content Created Successfully', true, 201);
            } else {

                $class = Content::where('id', $request->id)->first();
                if ($request->hasFile('icon')) {
                    Content::where('id', $request->id)->update([
                        'icon' => $this->imageUpload($request, 'icon', 'icon', $class->icon),
                        'thumbnail' => $this->imageUpload($request, 'thumbnail', 'thumbnail', $class->thumbnail)
                    ]);
                }

                $class->update($content);
                return $this->apiResponse([], 'Content Updated Successfully', true, 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function contentList()
    {
        $courseList = Content::leftJoin('categories', 'categories.id', 'contents.category_id')
            ->select(
                'contents.*',
                'categories.name as category_name',
            )
            ->get();
        return $this->apiResponse($courseList, 'Content List', true, 200);
    }

    public function saveOrUpdateContentOutline(Request $request)
    {
        try {
            $Content = [
                'title' => $request->title,
                'title_bn' => $request->title_bn,
                'content_id' => $request->content_id,
                'class_level_id' => $request->class_level_id,
                'subject_id'    => $request->subject_id,
                'chapter_id'   => $request->chapter_id,
                'chapter_script_id' => $request->chapter_script_id,
                'chapter_video_id' => $request->chapter_video_id,
                'chapter_quiz_id' => $request->chapter_quiz_id,
                'is_free'  => $request->is_free,
                'color_code' => $request->color_code,
                'sequence' => $request->sequence,
                'is_active ' => $request->is_active,
            ];

            if (empty($request->id)) {
                $contentList = ContentOutline::create($Content);
                if ($request->hasFile('icon')) {
                    $contentList->update([
                        'icon' => $this->imageUpload($request, 'icon', 'icon'),
                    ]);
                }
                return $this->apiResponse([], 'Content Outline Created Successfully', true, 201);
            } else {
                $content = ContentOutline::where('id', $request->id)->first();
                $content->update($Content);
                if ($request->hasFile('icon')) {
                    $content->update([
                        'icon' => $this->imageUpload($request, 'icon', 'icon', $content->icon),
                    ]);
                }
                return $this->apiResponse([], 'Content Outline Updated Successfully', true, 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function contentOutlineList(Request $request)
    {
        $id = $request->id ? $request->id : 0;
        $contentOutlineList = ContentOutline::leftJoin('contents', 'contents.id', 'content_outlines.content_id')
            ->leftJoin('class_levels', 'class_levels.id', 'content_outlines.class_level_id')
            ->leftJoin('subjects', 'subjects.id', 'content_outlines.subject_id')
            ->leftJoin('chapters', 'chapters.id', 'content_outlines.chapter_id')
            ->select(
                'content_outlines.*',
                'contents.title as content_name',
                'class_levels.name as class_level_name',
                'subjects.name as subject_name',
                'chapters.name as chapter_name',
            )
            ->when($id, function ($query, $id) {
                return $query->where('content_outlines.content_id', $id);
            })
            ->get();
        return $this->apiResponse($contentOutlineList, 'Content Outline List', true, 200);
    }
    public function contentOutlineDelete(Request $request)
    {
        try {
            contentOutline::where('id', $request->id)->delete();
            return $this->apiResponse([], 'Content Outline Deleted Successfully', true, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => []
            ], 500);
        }
    }
}
