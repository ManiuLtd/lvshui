<?php

namespace App\Http\Controllers\Api\Members;

use Illuminate\Http\Request;
use App\Models\MemberSetting;
use App\Http\Controllers\Controller;
use App\Http\Requests\SettingRequest;

class SettingController extends Controller
{
    public function index() 
    {
        $setting = MemberSetting::first();
        $setting['offer'] = json_decode($setting['offer']);        
        $groups = Group::getGroup();
        return response()->json(['status' => 'success', 'setting' => $setting, 'groups' => $groups]);   
    }

    public function store(SettingRequest $request) 
    {   
        $data = request()->all();  

        //满减
        // [
        //     ['group_id' => 1, 'condition'=> 100, 'discount'=>  10],
        //     ['group_id' =>  2, 'condition'=> 100, 'discount'=> 20]
        // ];

        //折扣
        // [
        //     ['group_id '=> 1, 'discount' => 9.5],
        //     ['group_id'=> 2, 'discount'=>  9]
        // ];

        $data['offer'] = json_encode($data['offer']);

        if(MemberSetting::create($data)) {
            return response()->json(['status' => 'success', 'msg' => '新增成功！']);                             
        }

        return response()->json(['status' => 'error', 'msg' => '新增失败！']);                           
        
    }

    public function show()
    {
        $setting = MemberSetting::find(request()->member_setting);
        $offer = json_decode($setting->offer,true);   
        $status = $setting ? 'success' : 'error';
        return response()->json(['status' => $status, 'data' => $setting]);   
    }

    public function update(SettingRequest $request)
    {
        $data = request()->all();   

        $data['offer'] = json_encode($data['offer']);
             
        if(MemberSetting::where('id', request()->member_setting)->update($data)) {
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);                             
        }

        return response()->json(['status' => 'error', 'msg' => '更新失败！']);                            
    }

    public function destroy()
    {
        if(MemberSetting::where('id', request()->member_setting)->delete()) {
            return response()->json(['status' => 'success', 'msg' => '删除成功！']);                              
        }

        return response()->json(['status' => 'error', 'msg' => '删除失败！']);     
    }

}
