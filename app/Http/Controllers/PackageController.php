<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Package;
use App\Models\PackageType;
use App\Models\PackageBenefitDetail;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function packageList(Request $request){
        $package_list = Package::select('id', 'title', 'description', 'limit', 'cycle', 'promotion_title', 'promotion_details', 'feature_image')->where('is_active', true)->get();

        foreach ($package_list as $item) {
            $item->benefits = PackageBenefitDetail::select('id', 'benefit')->where('package_id', $item->id)->get();
        }

        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' => $package_list
        ], 200);
    }

    public function packageDetailsByID(Request $request)
    {
        $package_id = $request->package_id ? $request->package_id : 0;

        $package_details = Package::select('id', 'title', 'description', 'limit', 'cycle', 'promotion_title', 'promotion_details', 'feature_image')->where('id', $package_id)->first();
        $package_details->benefits = PackageBenefitDetail::select('id', 'benefit')->where('package_id', $package_id)->get();

        return response()->json([
            'status' => true,
            'message' => 'Details Successful',
            'data' => $package_details
        ], 200);
    }

    public function adminBenefitListByID(Request $request)
    {
        $package_id = $request->package_id ? $request->package_id : 0;

        $benefits = PackageBenefitDetail::where('package_id', $package_id)->get();

        return response()->json([
            'status' => true,
            'message' => 'Details Successful',
            'data' => $benefits
        ], 200);
    }

    public function adminDeleteBenefitByID(Request $request)
    {
        $benefit_id = $request->id ? $request->id : 0;

        PackageBenefitDetail::where('id', $benefit_id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Benefit Deleted Successful',
            'data' => []
        ], 200);
    }

    public function saveOrUpdateBenefit (Request $request)
    {
        try {
            if($request->id){
                $benefit = PackageBenefitDetail::where('id', $request->id)->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Benefit has been updated successfully',
                    'data' => []
                ], 200);

            } else {
                $isExist = PackageBenefitDetail::where('benefit', $request->benefit)->first();
                if (empty($isExist)) {
                    $benefit = PackageBenefitDetail::create($request->all());
                    return response()->json([
                        'status' => true,
                        'message' => 'Benefit has been created successfully',
                        'data' => []
                    ], 200);
                }else{
                    return response()->json([
                        'status' => false,
                        'message' => 'Benefit already Exist!',
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

    public function adminPackageList(Request $request){
        $package_list = Package::all();

        foreach ($package_list as $item) {
            $item->benefits = PackageBenefitDetail::where('package_id', $item->id)->get();
        }

        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' => $package_list
        ], 200);
    }

    public function saveOrUpdatePackage (Request $request)
    {
        try {
            $formData = json_decode($request->data, true);
            if($formData['id']){
                $feature_url = null;
                if($request->hasFile('file')){
                    $image = $request->file('file');
                    $time = time();
                    $feature_image = "feature_image_" . $time . '.' . $image->getClientOriginalExtension();
                    $destinationProfile = 'uploads/package_image';
                    $image->move($destinationProfile, $feature_image);
                    $feature_url = $destinationProfile . '/' . $feature_image;
                }

                Package::where('id', $formData['id'])->update([
                    "title" => $formData['title'],
                    "description" => $formData['description'],
                    "limit" => $formData['limit'],
                    "cycle" => $formData['cycle'],
                    "promotion_title" => $formData['promotion_title'],
                    "promotion_details" => $formData['promotion_details'],
                    "is_active" => $formData['is_active']
                ]);

                if($request->hasFile('file')){
                    Package::where('id', $formData['id'])->update([
                        'feature_image' => $feature_url
                    ]);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Package has been updated successfully',
                    'data' => []
                ], 200);

            } else {
                $isExist = Package::where('title', $formData['title'])->first();
                if (empty($isExist)) 
                {
                    $feature_url = null;
                    if($request->hasFile('file')){
                        $image = $request->file('file');
                        $time = time();
                        $feature_image = "feature_image_" . $time . '.' . $image->getClientOriginalExtension();
                        $destinationProfile = 'uploads/package_image';
                        $image->move($destinationProfile, $feature_image);
                        $feature_url = $destinationProfile . '/' . $feature_image;
                    }

                    $package = Package::create([
                        "title" => $formData['title'],
                        "description" => $formData['description'],
                        "limit" => $formData['limit'],
                        "cycle" => $formData['cycle'],
                        "promotion_title" => $formData['promotion_title'],
                        "promotion_details" => $formData['promotion_details'],
                        "is_active" => $formData['is_active']
                    ]);

                    if($request->hasFile('file')){
                        Package::where('id', $package->id)->update([
                            'feature_image' => $feature_url
                        ]);
                    }

                    return response()->json([
                        'status' => true,
                        'message' => 'Package has been created successfully',
                        'data' => []
                    ], 200);
                }else{
                    return response()->json([
                        'status' => false,
                        'message' => 'Package already Exist!',
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
