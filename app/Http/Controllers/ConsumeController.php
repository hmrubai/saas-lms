<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Auth;
use App\Models\Payment;
use App\Models\Package;
use App\Models\PackageType;
use App\Models\TopicConsume;
use Illuminate\Http\Request;

class ConsumeController extends Controller
{
    public function myPackageList(Request $request)
    {
        $user_id = $request->user()->id;
        
        $package_list = TopicConsume::select(
                'topic_consumes.package_id', 
                'topic_consumes.payment_id',
                DB::raw("SUM(topic_consumes.balance) as balance"),
                DB::raw("SUM(topic_consumes.consumme) as consumme"),
                'topic_consumes.expiry_date', 
                'topic_consumes.created_at as purchased_date',
                'packages.title as packages_title', 
                'packages.feature_image', 
                'packages.description as packages_description',
            )
            ->where('user_id', $user_id)
            ->leftJoin('packages', 'packages.id', 'topic_consumes.package_id')
            ->leftJoin('package_types', 'package_types.id', 'topic_consumes.package_type_id')
            ->groupBy('topic_consumes.payment_id')
            ->get();
        
            foreach ($package_list as $item) {
                $packageDate = Carbon::parse($item->expiry_date);
                $now = Carbon::now();
                $item->balance = intval($item->balance);
                $item->consumme = intval($item->consumme);
    
                if ($now->gte($packageDate)) { 
                    $item->is_expired = true;
                }else{
                    $item->is_expired = false; 
                }
            }

        return response()->json([
            'status' => true,
            'message' => "Successful",
            'data' => $package_list
        ], 200);
    }

    public function myActiveSyllebusList(Request $request)
    {
        $payment_id = $request->payment_id ? $request->payment_id : 0;
        if(!$payment_id){
            return response()->json([
                'status' => false,
                'message' => 'Please, attach Package ID!',
                'data' => []
            ], 200);
        }

        $syllebus_list = TopicConsume::select('topic_consumes.package_type_id', 'package_types.name as syllebus_name', 'topic_consumes.balance', 'topic_consumes.consumme')
            ->where('payment_id', $payment_id)
            ->leftJoin('package_types', 'package_types.id', 'topic_consumes.package_type_id')
            ->get();

        return response()->json([
            'status' => true,
            'message' => "Successful",
            'data' => $syllebus_list
        ], 200);
    }

    public function myBalanceList(Request $request)
    {
        $user_id = $request->user()->id;

        $package = Package::all();

        $final_list = [];

        foreach ($package as $item) {
            $package_id = $item->id;
            $now_time = date("Y-m-d H:i:s");

            $package_list = TopicConsume::select('balance', 'consumme')
                ->where('user_id', $user_id)
                ->where('package_id', $package_id)
                ->whereDate('expiry_date', '>', $now_time)
                ->get();

            $balance = 0;
            if(sizeof($package_list)){
                $balance = $package_list->sum('balance') - $package_list->sum('consumme');
            }
            
            array_push($final_list, [
                "package_id" => $item->id,
                "title" => $item->title,
                "balance" => $balance
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => "Successful",
            'data' => $final_list
        ], 200);
    }

}
