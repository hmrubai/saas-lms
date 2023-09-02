<?php

namespace App\Http\Controllers;

use App\Http\Traits\HelperTrait;
use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Organization;
use App\Models\Setting;
use App\Models\WebsiteSetting;
use Illuminate\Support\Facades\Validator;

class OrganizationController extends Controller
{
    use HelperTrait;
    public function organizationList(Request $request)
    {
        $organization_list = Organization::select('id', 'name', 'slug', 'details', 'address', 'email', 'contact_no', 'logo', 'contact_person', 'is_active',)->where('is_active', true)->get();

        foreach ($organization_list as $item) {
            $item->settings = Setting::where('organization_slug', $item->slug)->first();
            $item->website_settings = WebsiteSetting::where('organization_id', $item->id)->first();
        }

        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' => $organization_list
        ], 200);
    }
    public function organizationListAdmin(Request $request)
    {
        $organization_list = Organization::select('id', 'name', 'slug', 'details', 'address', 'email', 'contact_no', 'logo', 'contact_person', 'is_active',)
        ->where('is_active', true)
        ->where('slug', auth()->user()->organization_slug)
        ->get();

        foreach ($organization_list as $item) {
            $item->settings = Setting::where('organization_slug', $item->slug)->first();
            $item->website_settings = WebsiteSetting::where('organization_id', $item->id)->first();
        }

        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' => $organization_list
        ], 200);
    }

    public function saveOrUpdateOrganization(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'slug' => 'required'
                ]
            );
            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'data' => $validateUser->errors()
                ], 422);
            }
            $user_id = $request->user()->id;
            if ($request->id) {

                $logo_image = null;
                $logo_url = null;
                if ($request->hasFile('logo')) {
                    $image = $request->file('logo');
                    $time = time();
                    $logo_image = "logo_image_" . $time . '.' . $image->getClientOriginalExtension();
                    $destination = 'uploads/logo';
                    $image->move($destination, $logo_image);
                    $logo_url = 'logo/' . $logo_image;
                }

                Organization::where('id', $request->id)->update([
                    "name" => $request->name,
                    //"slug" => $request->slug,
                    "details" => $request->details,
                    "address" => $request->address,
                    "email" => $request->email,
                    "user_id" => $user_id,
                    "contact_no" => $request->contact_no,
                    "contact_person" => $request->contact_person,
                    "is_active" => $request->is_active
                ]);

                Setting::where('organization_slug', $request->slug)->update([
                    "organization_name" => $request->name,
                    "contact_no" => $request->contact_no,
                    "is_active" => $request->is_active
                ]);

                $webSetting = WebsiteSetting::where('organization_id', $request->id)->first();
                $webSetting->update([
                    "contact_number" => $request->contact_no,
                    "hotline_number" => $request->hotline_number,
                    "email" => $request->email,
                ]);

                if ($request->hasFile('banner')) {
                    WebsiteSetting::where('organization_id', $request->id)->update([
                        'banner' => $this->imageUpload($request,'banner','banner',$webSetting->banner),
                    ]);
                }

                if ($request->hasFile('logo')) {
                    Organization::where('id', $request->id)->update([
                        'logo' => $logo_url
                    ]);

                    Setting::where('organization_slug', $request->slug)->update([
                        "logo" => $logo_url
                    ]);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Organization has been updated successfully',
                    'data' => []
                ], 200);
            } else {
                $logo_image = null;
                $logo_url = null;
                if ($request->hasFile('logo')) {
                    $image = $request->file('logo');
                    $time = time();
                    $logo_image = "logo_image_" . $time . '.' . $image->getClientOriginalExtension();
                    $destination = 'uploads/logo';
                    $image->move($destination, $logo_image);
                    $logo_url = 'logo/' . $logo_image;
                }

                $isExist = Organization::where('name', $request->slug)->first();
                if (empty($isExist)) {
                    $organization = Organization::create([
                        "name" => $request->name,
                        "slug" => $request->slug,
                        "details" => $request->details,
                        "address" => $request->address,
                        "email" => $request->email,
                        "user_id" => $user_id,
                        "contact_no" => $request->contact_no,
                        "contact_person" => $request->contact_person,
                        "is_active" => $request->is_active
                    ]);

                    $settings = Setting::create([
                        "organization_name" => $request->name,
                        "organization_slug" => $request->slug,
                        "host_url" => 'host_url',
                        "asset_host" => 'asset_host',
                        "user_id" => $user_id,
                        "color_theme" => '#346beb',
                        "contact_no" => $request->contact_no,
                        "is_active" => $request->is_active
                    ]);

                    $webSetting = WebsiteSetting::create([
                        "organization_id" => $organization->id,
                        "contact_number" => $request->contact_no,
                        "hotline_number" => $request->hotline_number,
                        "email" => $request->email,
                    ]);



                    if ($request->hasFile('banner')) {
                        $webSetting->update([
                            'banner' => $this->imageUpload($request, 'banner', 'banner'),
                        ]);
                    }

                    if ($request->hasFile('logo')) {
                        $organization->update([
                            'logo' => $logo_url
                        ]);

                        $settings->update([
                            'logo' => $logo_url
                        ]);
                    }
                    return response()->json([
                        'status' => true,
                        'message' => 'Organization has been created successfully',
                        'data' => []
                    ], 200);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Organization already Exist!',
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

    public function updateSettings(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'id' => 'required',
                    'host_url' => 'required',
                    'asset_host' => 'required',
                    'color_theme' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'data' => $validateUser->errors()
                ], 422);
            }

            $user_id = $request->user()->id;

            Setting::where('id', $request->id)->update([
                "host_url" => $request->host_url,
                "asset_host" => $request->asset_host,
                "user_id" => $user_id,
                "color_theme" => $request->color_theme
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Settings has been updated successfully',
                'data' => []
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 200);
        }
    }
}
