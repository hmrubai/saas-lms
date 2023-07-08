<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PromotionalNotice;

class PromotionalNoticeController extends Controller
{
    public function promotionalNoticeList(Request $request){
        $notice_list = PromotionalNotice::select('id', 'title', 'description', 'navigation_link', 'feature_image')->where('is_active', true)->get();

        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' => $notice_list
        ], 200);
    }

    public function adminPromotionalNoticeList(Request $request){
        $notice = PromotionalNotice::all();

        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' => $notice
        ], 200);
    }

    public function saveOrUpdatePromotionalNotice (Request $request)
    {
        try {
            $formData = json_decode($request->data, true);
            if($formData['id']){
                $feature_url = null;
                if($request->hasFile('file')){
                    $image = $request->file('file');
                    $time = time();
                    $feature_image = "feature_image_" . $time . '.' . $image->getClientOriginalExtension();
                    $destinationProfile = 'uploads/news';
                    $image->move($destinationProfile, $feature_image);
                    $feature_url = $destinationProfile . '/' . $feature_image;
                }

                $notice = PromotionalNotice::where('id', $formData['id'])->update([
                    "title" => $formData['title'],
                    "description" => $formData['description'],
                    "navigation_link" => $formData['navigation_link'],
                    "is_active" => $formData['is_active']
                ]);

                if($request->hasFile('file')){
                    PromotionalNotice::where('id', $formData['id'])->update([
                        'feature_image' => $feature_url
                    ]);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Promotional Notice has been updated successfully',
                    'data' => []
                ], 200);

            } else {
                $isExist = PromotionalNotice::where('title', $formData['title'])->first();
                if (empty($isExist)) 
                {
                    $feature_url = null;
                    if($request->hasFile('file')){
                        $image = $request->file('file');
                        $time = time();
                        $feature_image = "feature_image_" . $time . '.' . $image->getClientOriginalExtension();
                        $destinationProfile = 'uploads/news';
                        $image->move($destinationProfile, $feature_image);
                        $feature_url = $destinationProfile . '/' . $feature_image;
                    }

                    $notice = PromotionalNotice::create([
                        "title" => $formData['title'],
                        "description" => $formData['description'],
                        "navigation_link" => $formData['navigation_link'],
                        "is_active" => $formData['is_active']
                    ]);

                    if($request->hasFile('file')){
                        PromotionalNotice::where('id', $notice->id)->update([
                            'feature_image' => $feature_url
                        ]);
                    }

                    return response()->json([
                        'status' => true,
                        'message' => 'Promotional Notice has been created successfully',
                        'data' => []
                    ], 200);
                }else{
                    return response()->json([
                        'status' => false,
                        'message' => 'Promotional Notice already Exist!',
                        'data' => []
                    ], 200);
                }
            }

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 200);
        }
    }
}
