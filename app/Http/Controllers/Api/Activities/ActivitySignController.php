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
use App\Models\Fan;
use Carbon\Carbon;

class ActivitySignController extends Controller
{
     public function index()
     {
         $activitys=Activity::orderBy('created_at','desc')->paginate(20);
         return response()->json(['status' => 'success', 'data' => $activitys]);
     }

     public function show()
     {
         $activity=Activity::with(['fans'=>function($query){
             $fan_id=request()->fan_id;
             $query->where('fan_id',$fan_id)->get();
         }])->withCount('fans')->find(request()->activity);
         $today=Carbon::parse()->toDateString();
         $fan_id=Token::getUid();
         $fan=Fan::find($fan_id);
         $sign_buttn=true;
//         if ($activity->privilege){
//             $sign_buttn='资格不足';
//         }
         if(count($activity->fans)){
             $sign_buttn='你已报名';
         }
         if($activity->fans_count==$activity->places&&$activity->places!=0){
             $sign_buttn='人数已满';
         }
         if ($activity->sign_end_time	==$today){
             $sign_buttn='报名结束';
         }
         return response()->json(['status' => 'success',
             'msg' =>compact('sign_buttn','activity')]);
     }

     public function signIn()
     {
        $fan_id=Token::getUid();
        $activity=Activity::withCount('fans')->find(request()->avtivity_id);
        $activity_sign_count=$activity->fans_count;
        if($activity_sign_count>=$activity->places&&$activity->places!=0){
            return response()->json(['status' => 'error', 'msg' => '报名人数已满']);
        }
        $sign_in=$activity->fans()
        ->attach($fan_id,['name'=>request()->name,'contact_way'=>request()->contact_way]);
         return response()->json(['status' => 'success', 'msg' => '更新成功！']);
     }


}