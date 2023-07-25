<?php

namespace App\Http\Traits;

use Validator;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Hash;

trait HelperTrait
{
    protected function apiResponse($data = null, $message = null, $status = null, $statusCode = null)
    {
        $array = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($array, $statusCode);
    }
    
    protected function codeGenerator($prefix, $model)
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

    protected function imageUpload($request, $image, $destination, $old_image = null)
    {
        $images = null;
        $images_url = null;
        if ($request->hasFile($image)) {
            if ($old_image != null) {
                $old_image_path = public_path('uploads/' . $old_image);
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }
            $image = $request->file($image);
            $time = time();
            $images = "bb_" . $time . '.' . $image->getClientOriginalExtension();
            $destinations = 'uploads/' . $destination;
            $image->move($destinations, $images);
            $images_url = $destination . '/' . $images;
        }
        return $images_url;
    }


    protected function imageUploadWithPrefix($request, $image, $destination,$prefix, $old_image = null,)
    {
        $images = null;
        $images_url = null;
        if ($request->hasFile($image)) {
            if ($old_image != null) {
                $old_image_path = public_path('uploads/' . $old_image);
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }
            $image = $request->file($image);
            $time = time();
            $images = $prefix. '_' . $time . '.' . $image->getClientOriginalExtension();
            
            $destinations = 'uploads/' . $destination;
            $image->move($destinations, $images);
            $images_url = $destination . '/' . $images;
        }
        return $images_url;
    }


}
