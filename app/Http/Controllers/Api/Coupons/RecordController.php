<?php

namespace App\Http\Controllers\Api\Coupons;

use App\Models\Admin;
use App\Models\Coupon;
use App\Services\Token;
use App\Models\CouponRecord;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\RecordRequest;
use Illuminate\Support\Facades\Validator;

class RecordController extends Controller
{
    
    public function index() 
    {
        $fan_id = request()->fan_id ?? 0;
        $coupon_id = request()->coupon_id ?? 0;
        $status = request()->status ?? -2;
        $records = CouponRecord::orderBy('created_at','desc')->when($fan_id > 0, function($query) use ($fan_id) {
            return $query->where('fan_id', $fan_id);
        })->when($coupon_id > 0, function($query) use ($coupon_id) {
            return $query->where('coupon_id', $coupon_id);
        })->when($status > -2, function($query) use ($status) {
            return $query->where('status', $status);
        })->paginate(config('common.pagesize')); 
        $records->load('fan','coupon');  
        return response()->json(['status' => 'success', 'data' => $records]);   
    }

    public function store(RecordRequest $request) 
    {   
        $data = request()->all();        
        $time = Coupon::getTime($request->coupon_id);
        $data['start_time'] = $time['start'];
        $data['end_time'] = $time['end'];
        if(CouponRecord::create($data)) {
            return response()->json(['status' => 'success', 'msg' => '新增成功！']);                             
        }

        return response()->json(['status' => 'error', 'msg' => '新增失败！']);                           
        
    }

    public function show()
    {
        $record = CouponRecord::with(['coupon'])->find(request()->record);
        $status = $record ? 'success' : 'error';
        return response()->json(['status' => $status, 'data' => $record]);   
    }

    public function update(RecordRequest $request)
    {
        $data = request()->all();        
        $time = Coupon::getTime($request->coupon_id);
        $data['start_time'] = $time['start'];
        $data['end_time'] = $time['end'];
        if(CouponRecord::where('id', request()->record)->update($data)) {
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);                             
        }

        return response()->json(['status' => 'error', 'msg' => '更新失败！']);                            
    }

    public function destroy()
    {
        if(CouponRecord::where('id', request()->record)->delete()) {
            return response()->json(['status' => 'success', 'msg' => '删除成功！']);                              
        }

        return response()->json(['status' => 'error', 'msg' => '删除失败！']);     
    }

    public function get_user_coupons()
    {
        $use = CouponRecord::getUserHasCoupons(Token::getUid());
        $used = CouponRecord::getUserCouponsByUsed(Token::getUid());
        return response()->json(['status' => 'success', 'use' => $use, 'used' => $used]);    
    }

    public function verification()
    {
        $record = CouponRecord::with(['coupon','fan'])->find(request()->record_id);
        return response()->json(['status' => 'success', 'record' => $record]);   
    }

    public function confirmVerification()
    {
        //TODO 判断是否是管理员进行核销
        $admin = Admin::where('fan_id',Token::getUid())->first();
        if(!isset($admin) || \Auth::guard('users')->id()) {
            return response()->json(['status' => 'error', 'msg' => '你不是管理员，无操作权限']);   
        }
        $ret = CouponRecord::use(request()->record_id);
        return response()->json(['status' => $ret]);   
    }


}
