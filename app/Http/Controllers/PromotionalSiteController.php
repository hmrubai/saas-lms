<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PromotionalSiteController extends Controller
{

    public function clientInfoSave(Request $request){
        $client_info = ClientInfo::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'occupation' => $request->occupation,
            'organization_name'=> $request->organization_name,
            'organization_address'=> $request->organization_address,
            'nid_passport'=> $request->nid_passport,
            'trade_license'=> $request->trade_license,
            'web_address'=> $request->web_address,
            'post_code'=> $request->post_code,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Client Info Save Successful',
            'data' => $client_info
        ], 200);
    }


}
