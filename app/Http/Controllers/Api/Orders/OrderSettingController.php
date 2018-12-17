<?php

namespace App\Http\Controllers\Api\Orders;

use App\Models\OrderSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class OrderSettingController extends Controller
{
    //
    public function index()
    {
        $orderSetting = OrderSetting::all();
        return response()->json(['data' => $orderSetting]);
    }

    public function store()
    {
        $list = request(['type', 'date']);
        DB::beginTransaction();
        try {
            if ($list['type'] == 'day') {
                OrderSetting::where('type', 'day')->updat(['switch' => 1, 'day' => 'date']);
                OrderSetting::where('type', 'date')->updat(['switch' => 0]);
            }else if ($list['type'] == 'date') {
                OrderSetting::where('type', 'day')->updat(['switch' => 0]);
                OrderSetting::where('type', 'date')->updat(['switch' => 1, 'date' => 'date']);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '修改失败' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '修改成功！']);
    }

    public function getOrderSetting()
    {
        $orderSetting = OrderSetting::where('switch',1)->first();
        return response()->json(['data' => $orderSetting]);
    }

}
