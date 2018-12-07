<?php

namespace App\Http\Controllers\Api\Malls;

use App\Models\MallSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MallSettingController extends Controller
{
    //
    public function index(){
        $setting = MallSetting::all();
        return response()->json(['data' => $setting]);
    }
    public function store()
    {
        $rdata = request(['switch','radio']);
        $first =  MallSetting::first();
        if($first){
            DB::beginTransaction();
            try {
                MallSetting::first()->update($rdata);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['status' => 'error', 'msg' => '修改失败' . $e]);
            }
            return response()->json(['status' => 'success', 'msg' => '修改成功！']);
        }else{
            DB::beginTransaction();
            try {
                MallSetting::create($rdata);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['status' => 'error', 'msg' => '新增失败' . $e]);
            }
            return response()->json(['status' => 'success', 'msg' => '新增成功！']);
        }
    }
}
