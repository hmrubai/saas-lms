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
                $images = null;
                $images_url = null;
                if($request->hasFile('icon')){
                    $image = $request->file('icon');
                    $time = time();
                    $images = "image_icon_" . $time . '.' . $image->getClientOriginalExtension();
                    $destination = 'uploads/icon';
                    $image->move($destination, $images);
                    $images_url = 'icon/' . $images;
                }

               $classList= ClassLevel::create([
                    'name' => $request->name,
                    'name_bn' => $request->name_bn,
                    'class_code' => $this->codeGenerator('CC', ClassLevel::class),
                    'price' => $request->price,
                    'is_free' => $request->is_free,
                    'color_code' => $request->color_code,
                    'sequence' => $request->sequence,
                    'is_active' => $request->is_active,
                ]);

                if($request->hasFile('icon')){
                    $classList->update([
                        'icon' => $images_url
                    ]);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Class Created Successfully',
                    'data' => []
                ], 200);
            } else {
                $images = null;
                $images_url = null;
                if($request->hasFile('icon')){
                    $image = $request->file('icon');
                    $time = time();
                    $images = "image_icon_" . $time . '.' . $image->getClientOriginalExtension();
                    $destination = 'uploads/logo';
                    $image->move($destination, $images);
                    $images_url = 'logo/' . $images;
                }
               ClassLevel::where('id', $request->id)->update([
                    "name" => $request->name,
                    "name_bn" => $request->name_bn,
                    "class_code" => $this->codeGenerator('CC', ClassLevel::class),
                    "price" => $request->price,
                    "is_free" => $request->is_free,
                    "color_code" => $request->color_code,
                    "sequence" => $request->sequence,
                    "is_active" => $request->is_active,
                ]);

                if($request->hasFile('icon')){
                    ClassLevel::where('id', $request->id)->update([
                        'icon' => $images_url
                    ]);
                }

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

                $images = null;
                $images_url = null;
                if($request->hasFile('icon')){
                    $image = $request->file('icon');
                    $time = time();
                    $images = "logo_image_" . $time . '.' . $image->getClientOriginalExtension();
                    $destination = 'uploads/icon';
                    $image->move($destination, $images);
                    $images_url = 'icon/' . $images;
                }

                $subjectList =Subject::create([
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

              if($request->hasFile('icon')){
                    $subjectList->update([
                        'icon' => $images_url
                    ]);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Subject Created Successfully',
                    'data' => []
                ], 200);
            } else {

                $images = null;
                $images_url = null;
                if($request->hasFile('icon')){
                    $image = $request->file('icon');
                    $time = time();
                    $images = "logo_image_" . $time . '.' . $image->getClientOriginalExtension();
                    $destination = 'uploads/icon';
                    $image->move($destination, $images);
                    $images_url = 'icon/' . $images;
                }

                Subject::where('id', $request->id)->update([
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
                if($request->hasFile('icon')){
                    Subject::where('id', $request->id)->update([
                        'icon' => $images_url
                    ]);

                }
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

                $images = null;
                $images_url = null;
                if($request->hasFile('icon')){
                    $image = $request->file('icon');
                    $time = time();
                    $images = "logo_image_" . $time . '.' . $image->getClientOriginalExtension();
                    $destination = 'uploads/icon';
                    $image->move($destination, $images);
                    $images_url = 'icon/' . $images;
                }

                $chapterList=Chapter::create([
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
                if($request->hasFile('icon')){
                    $chapterList->update([
                        'icon' => $images_url
                    ]);
                }


                return response()->json([
                    'status' => true,
                    'message' => 'Chapter Created Successfully',
                    'data' => []
                ], 200);
            } else {

                $images = null;
                $images_url = null;
                if($request->hasFile('icon')){
                    $image = $request->file('icon');
                    $time = time();
                    $images = "logo_image_" . $time . '.' . $image->getClientOriginalExtension();
                    $destination = 'uploads/icon';
                    $image->move($destination, $images);
                    $images_url = 'icon/' . $images;
                }

                Chapter::where('id', $request->id)->update([
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
                if($request->hasFile('icon')){
                    Chapter::where('id', $request->id)->update([
                        'icon' => $images_url
                    ]);

                }

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
        $videoChapterList = ChapterVideo::select(
                'id',
                "title",
                "title_bn",
                "video_code",
                "class_level_id",
                "subject_id",
                "chapter_id",
                "video_code",
                "author_name",
                "author_details",
                "description",
                "raw_url",
                "s3_url",
                "youtube_url",
                "download_url",
                "thumbnail",
                "duration",
                "price",
                "rating",
                "is_free",
                "sequence",
                "is_active",
            )->get();
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

                ChapterVideo::create([
                    "title" => $request->title,
                    "title_bn" => $request->title_bn,
                    "video_code" => $request->video_code,
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
                    "thumbnail" => $request->thumbnail,
                    "duration" => $request->duration,
                    "price" => $request->price,
                    "rating" => $request->rating,
                    "is_free" => $request->is_free,
                    "sequence" => $request->sequence,
                    "is_active" => $request->is_active,
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Chapter Video Created Successfully',
                    'data' => []
                ], 200);
            } else {

                ChapterVideo::where('id', $request->id)->update([
                    "title" => $request->title,
                    "title_bn" => $request->title_bn,
                    "video_code" => $request->video_code,
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
                    "thumbnail" => $request->thumbnail,
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
