<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\ClassLevel;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContentController extends Controller
{
    function codeGenerator($prefix, $model)
    {
        if ($model::count() == 0) {
            $newId = $prefix . str_pad(1, 5, 0, STR_PAD_LEFT);
            return $newId;
        }

        $lastId = $model::orderBy('id', 'desc')->first()->id;
        $lastIncrement = substr($lastId, -3);
        $newId = $prefix . str_pad($lastIncrement + 1, 5, 0, STR_PAD_LEFT);
        $newId++;
        return $newId;
    }

    public function classList()
    {
        $classList = ClassLevel::select('id','name', 'name_bn', 'class_code', 'price', 'is_free', 'icon', 'color_code', 'sequence', 'is_active')->get();
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

                ClassLevel::create([
                    'name' => $request->name,
                    'name_bn' => $request->name_bn,
                    'class_code' => $this->codeGenerator('CC', ClassLevel::class),
                    'price' => $request->price,
                    'is_free' => $request->is_free,
                    'icon' => $request->icon,
                    'color_code' => $request->color_code,
                    'sequence' => $request->sequence,
                    'is_active' => $request->is_active,
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Class Created Successfully',
                    'data' => []
                ], 200);
            } else {
                ClassLevel::where('id', $request->id)->update([
                    "name" => $request->name,
                    "name_bn" => $request->name_bn,
                    "class_code" => $this->codeGenerator('CC', ClassLevel::class),
                    "price" => $request->price,
                    "is_free" => $request->is_free,
                    "icon" => $request->icon,
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
        $subjectList = ClassLevel::select('id','name' ,'name_bn','class_level_id', 'subject_code', 'price', 'is_free', 'icon', 'color_code', 'sequence', 'is_active')->get();
        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' =>    $subjectList
        ], 200);
    }

    public function saveOrUpdateSubject(Request $request)
    {
        try {

            if (empty($request->id)) {

                Subject::create([
                    "name" => $request->name,
                    "name_bn" => $request->name_bn,
                    "class_level_id" => $request->class_level_id,
                    "subject_code" => $this->codeGenerator('SC', Subject::class),
                    "price" => $request->price,
                    "is_free" => $request->is_free,
                    "icon" => $request->icon,
                    "color_code" => $request->color_code,
                    "sequence" => $request->sequence, 
                    "is_active" => $request->is_active,
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Subject Created Successfully',
                    'data' => []
                ], 200);
            } else {

                Subject::where('id', $request->id)->update([
                    "name" => $request->name,
                    "name_bn" => $request->name_bn,
                    "class_level_id" => $request->class_level_id,
                    "subject_code" => $this->codeGenerator('SC', Subject::class),
                    "price" => $request->price,
                    "is_free" => $request->is_free,
                    "icon" => $request->icon,
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
        $chapterList = ClassLevel::select('id','name', 'name_bn','class_level_id','subject_id','chapter_code', 'price', 'is_free', 'icon', 'color_code', 'sequence', 'is_active')->get();
        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' =>    $chapterList
        ], 200);
    }
    
    public function saveOrUpdateChapter(Request $request)
    {
        try {

            if (empty($request->id)) {

                Chapter::create([
                    "name" => $request->name,
                    "name_bn" => $request->name_bn,
                    "class_level_id" => $request->class_level_id,
                    "subject_id" => $request->subject_id,
                    "chapter_code" => $this->codeGenerator('CHC', Chapter::class),
                    "price" => $request->price,
                    "is_free" => $request->is_free,
                    "icon" => $request->icon,
                    "color_code" => $request->color_code,
                    "sequence" => $request->sequence, 
                    "is_active" => $request->is_active,
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Chapter Created Successfully',
                    'data' => []
                ], 200);
            } else {

                Chapter::where('id', $request->id)->update([
                        "name" => $request->name,
                        "name_bn" => $request->name_bn,
                        "class_level_id" => $request->class_level_id,
                        "subject_id" => $request->subject_id,
                        "chapter_code" => $this->codeGenerator('CHC', Chapter::class),
                        "price" => $request->price,
                        "is_free" => $request->is_free,
                        "icon" => $request->icon,
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
}
