<?php
/**
 * Created by PhpStorm.
 * User: 29673
 * Date: 2018/11/12
 * Time: 9:44
 */

namespace App\Http\Controllers\Api\Activities;

use App\Http\Controllers\Controller;
use App\Models\Activity;

class ActivitySignController extends Controller
{
     public function show()
     {

     }

     public function signIn()
     {
        $fan_id='';
        $activity=Activity::find(request()->avtivity_id);
        $activity_sign_count=$activity->withCount();
        if($activity_sign_count>=$activity->places){
            return response()->json(['status' => 'error', 'msg' => '报名人数已满']);
        }
        if($activity->sign_type==1){
            //微信支付
        }
        $sign_in=$activity->fan()
        ->attach($fan_id,['name'=>request()->name,'contact_way'=>request()->contact_way]);
         return response()->json(['status' => 'success', 'msg' => $sign_in]);
     }


}