<?php

namespace App\Http\Controllers\Api\Members;

use Illuminate\Http\Request;
use App\Models\MemberJoinSetting;
use App\Http\Controllers\Controller;
use App\Http\Requests\SettingRequest;

class JoinSettingController extends Controller
{
    public function index() 
    {
        $setting = MemberJoinSetting::get();
        return response()->json(['status' => 'success', 'setting' => $setting]);   
    }

    public function store(JoinSettingRequest $request) 
    {   
        $data = request()->all();  

        if(MemberJoinSetting::create($data)) {
            return response()->json(['status' => 'success', 'msg' => '新增成功！']);                             
        }

        return response()->json(['status' => 'error', 'msg' => '新增失败！']);                           
        
    }

    public function show()
    {
        $setting = MemberJoinSetting::find(request()->setting);
        $status = $setting ? 'success' : 'error';
        return response()->json(['status' => $status, 'data' => $setting]);   
    }

    public function update(JoinSettingRequest $request)
    {
        $data = request()->all();   
             
        if(MemberJoinSetting::where('id', request()->setting)->update($data)) {
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);                             
        }

        return response()->json(['status' => 'error', 'msg' => '更新失败！']);                            
    }

    public function destroy()
    {
        if(MemberJoinSetting::where('id', request()->setting)->delete()) {
            return response()->json(['status' => 'success', 'msg' => '删除成功！']);                              
        }

        return response()->json(['status' => 'error', 'msg' => '删除失败！']);     
    }

}
