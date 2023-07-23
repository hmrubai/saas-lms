<?php

namespace App\Http\Controllers;

use App\Http\Traits\HelperTrait;
use App\Models\Chapter;
use App\Models\ChapterVideo;
use App\Models\ClassLevel;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContentController extends Controller
{
    use HelperTrait;

    public function subjectListByClassID(Request $request)
    {
        $class_id = $request->class_id;
        $subjectList = Subject::select('id', 'name', 'name_bn', 'class_level_id')->where('class_level_id', $class_id)->get();
        return response()->json([
            'status' => true,
            'message' => 'Subject List Successful',
            'data' =>    $subjectList
        ], 200);
    }

    public function chapterListBySubjectID(Request $request)
    {
        $subject_id = $request->subject_id;
        $subjectList = Chapter::select('id', 'name', 'name_bn', 'subject_id')->where('subject_id', $subject_id)->get();
        return response()->json([
            'status' => true,
            'message' => 'Chapter List Successful',
            'data' =>    $subjectList
        ], 200);
    }

    public function classList()
    {
        $classList = ClassLevel::select('id', 'name', 'name_bn', 'class_code', 'price', 'is_free', 'icon', 'color_code', 'sequence', 'is_active')->get();
        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' =>   $classList
        ], 200);
    }

    public function saveOrUpdateClass(Request $request)
    {
        try {
            if (empty($request->id)) {
                $classList = ClassLevel::create([
                    'name' => $request->name,
                    'name_bn' => $request->name_bn,
                    'class_code' => $this->codeGenerator('CC', ClassLevel::class),
                    'price' => $request->price,
                    'is_free' => $request->is_free,
                    'color_code' => $request->color_code,
                    'sequence' => $request->sequence,
                    'is_active' => $request->is_active,
                ]);

                if ($request->hasFile('icon')) {
                    $classList->update([
                        'icon' => $this->imageUpload($request, 'icon', 'icon')
                    ]);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Class Created Successfully',
                    'data' => []
                ], 200);
            } else {
                $class = ClassLevel::where('id', $request->id)->first();
                if ($request->hasFile('icon')) {
                    ClassLevel::where('id', $request->id)->update([
                        'icon' => $this->imageUpload($request, 'icon', 'icon', $class->icon)
                    ]);
                }
                $class->update([
                    "name" => $request->name,
                    "name_bn" => $request->name_bn,
                    "class_code" => $this->codeGenerator('CC', ClassLevel::class),
                    "price" => $request->price,
                    "is_free" => $request->is_free,
                    "color_code" => $request->color_code,
                    "sequence" => $request->sequence,
                    "is_active" => $request->is_active,
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Class Updated Successfully',
                    'data' => []
                ], 200);
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
        $subjectList = Subject::select('id', 'name', 'name_bn', 'class_level_id', 'subject_code', 'price', 'is_free', 'icon', 'color_code', 'sequence', 'is_active')->get();
        return response()->json([
            'status' => true,
            'message' => 'Subject List Successful',
            'data' =>    $subjectList
        ], 200);
    }

    public function saveOrUpdateSubject(Request $request)
    {
        try {
            if (empty($request->id)) {
                $subjectList = Subject::create([
                    "name" => $request->name,
                    "name_bn" => $request->name_bn,
                    "class_level_id" => $request->class_level_id,
                    "subject_code" => $this->codeGenerator('SC', Subject::class),
                    "price" => $request->price,
                    "is_free" => $request->is_free,
                    "color_code" => $request->color_code,
                    "sequence" => $request->sequence,
                    "is_active" => $request->is_active,
                ]);

                if ($request->hasFile('icon')) {
                    $subjectList->update([
                        'icon' => $this->imageUpload($request, 'icon', 'icon')
                    ]);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Subject Created Successfully',
                    'data' => []
                ], 200);
            } else {
                $subject = Subject::where('id', $request->id)->first();
                if ($request->hasFile('icon')) {
                    Subject::where('id', $request->id)->update([
                        'icon' => $this->imageUpload($request, 'icon', 'icon', $subject->icon)
                    ]);
                }
                $subject->update([
                    "name" => $request->name,
                    "name_bn" => $request->name_bn,
                    "class_level_id" => $request->class_level_id,
                    "subject_code" => $this->codeGenerator('SC', Subject::class),
                    "price" => $request->price,
                    "is_free" => $request->is_free,
                    "color_code" => $request->color_code,
                    "sequence" => $request->sequence,
                    "is_active" => $request->is_active,
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Subject Updated Successfully',
                    'data' => []
                ], 200);
            }
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => []
            ], 200);
        }
    }

    public function chapterList()
    {
        $chapterList = Chapter::select('id', 'name', 'name_bn', 'class_level_id', 'subject_id', 'chapter_code', 'price', 'is_free', 'icon', 'color_code', 'sequence', 'is_active')->get();
        return response()->json([
            'status' => true,
            'message' => 'Chapter List Successful',
            'data' =>    $chapterList
        ], 200);
    }

    public function saveOrUpdateChapter(Request $request)
    {
        try {
            if (empty($request->id)) {
                $chapterList = Chapter::create([
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
                if ($request->hasFile('icon')) {
                    $chapterList->update([
                        'icon' => $this->imageUpload($request, 'icon', 'icon')
                    ]);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Chapter Created Successfully',
                    'data' => []
                ], 200);
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
                return response()->json([
                    'status' => true,
                    'message' => 'Chapter Updated Successfully',
                    'data' => []
                ], 200);
            }
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => []
            ], 200);
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
        return response()->json([
            'status' => true,
            'message' => 'Chapter List Successful',
            'data' =>    $videoChapterList
        ], 200);
    }

    public function saveOrUpdateChapterVideo(Request $request)
    {
        try {
            if (empty($request->id)) {
                $chapterList = ChapterVideo::create([
                    "title" => $request->title,
                    "title_bn" => $request->title_bn,
                    "class_level_id" => $request->class_level_id,
                    "subject_id" => $request->subject_id,
                    "chapter_id" => $request->chapter_id,
                    "video_code" => $this->codeGenerator('CVC', Chapter::class),
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
                ]);
                if ($request->hasFile('thumbnail')) {
                    $chapterList->update([
                        'thumbnail' => $this->imageUpload($request, 'thumbnail', 'thumbnail')
                    ]);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Chapter Video Created Successfully',
                    'data' => []
                ], 200);
            } else {

                $video = ChapterVideo::where('id', $request->id)->first();
                if ($request->hasFile('thumbnail')) {
                    ChapterVideo::where('id', $request->id)->update([
                        'thumbnail' => $this->imageUpload($request, 'thumbnail', 'thumbnail', $video->thumbnail)
                    ]);
                }
                $video->update([
                    "title" => $request->title,
                    "title_bn" => $request->title_bn,
                    "class_level_id" => $request->class_level_id,
                    "subject_id" => $request->subject_id,
                    "chapter_id" => $request->chapter_id,
                    "video_code" => $this->codeGenerator('CVC', Chapter::class),
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
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Chapter Video Updated Successfully',
                    'data' => []
                ], 200);
            }
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => []
            ], 200);
        }
    }
}
