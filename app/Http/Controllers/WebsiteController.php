<?php

namespace App\Http\Controllers;

use App\Http\Traits\HelperTrait;
use App\Models\Setting;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;

class WebsiteController extends Controller

{
    use HelperTrait;

    public function websiteSettingSaveOrUpdate(Request $request)
    {
        try {

            $webSetting=[
                'banner' => $request->banner,
                'contact_number' => $request->contact_number,
                'hotline_number' => $request->hotline_number,
                'email' => $request->email,
            ];
            
            if (empty($request->id)) {
                WebsiteSetting::create($webSetting);
                $logo= new Setting();
                $logo->logo=$request->logo;
                $logo->save(); 

                return $this->apiResponse([], 'Website Setting Created Successfully', 200);


            }
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), 500);
        }
    }
}
