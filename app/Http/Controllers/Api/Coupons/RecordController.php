<?php

namespace App\Http\Controllers\Api\Coupons;

use App\Models\Coupon;
use App\Services\Token;
use App\Models\CouponRecord;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\RecordRequest;

class RecordController extends Controller
{
    
    public function index() 
    {
        $records = CouponRecord::orderBy('created_at','desc')->paginate(config('common.pagesize')); 
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
        $record = CouponRecord::with(['coupon'])->find(request()->record_id);
        $fan = Fan::find(request()->fan_id);
        return response()->json(['status' => 'success', 'record' => $record, 'fan' => $fan]);   
    }

    public function confirmVerification()
    {
        //TODO 判断是否是管理员进行核销
        $admin = Admin::where('fan_id',Token::getUid())->first();
        if(!isset($admin)) {
            return response()->json(['status' => 'error', 'msg' => '你不是管理员，无操作权限']);   
        }
        $ret = CouponRecord::use(request()->record_id);
        return response()->json(['status' => $ret]);   
    }

}
