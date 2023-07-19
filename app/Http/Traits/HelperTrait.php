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
}
