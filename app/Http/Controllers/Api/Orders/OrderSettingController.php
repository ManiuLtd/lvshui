<?php

namespace App\Http\Controllers\Api\Orders;

use App\Models\OrderSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderSettingController extends Controller
{
    //
    public function index()
    {
        $orderSetting = OrderSetting::orderBy('created_at', 'desc')->paginate(20);
        return response()->json(['data' => $orderSetting]);
    }

    public function store()
    {
        $end_date = request('end_date');
        DB::beginTransaction();
        try {

            OrderSetting::create(['end_date' => $end_date]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '新增失败' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '新增成功！']);
    }

    public function update()
    {
        $id = request()->order;
        $end_date = request('end_date');
        DB::beginTransaction();
        try {
            OrderSetting::where('id',$id)->update(['end_date' => $end_date]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '修改失败' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '修改成功！']);
    }

}
