<?php

namespace App\Http\Traits;

use Validator;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Hash;

trait HelperTrait
{
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


    protected function uploadImage($request, $icon, $destination,$folder)
    {
        $image_name = null;
        $image_url = null;
        if ($request->hasFile($icon)) {
            $image = $request->file($icon);
            $time = time();
            $image_name = $image . "_" . $time . '.' . $image->getClientOriginalExtension();
            $destination = $destination;
            $image->move($destination, $image_name);
            $image_url = $folder . $image_name;
        }
        return $image_url;
    }


}
