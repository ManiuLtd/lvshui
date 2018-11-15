<?php

namespace App\Http\Controllers\Api\Members;

use App\Models\JoinSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SettingRequest;
use App\Http\Requests\JoinSettingRequest;

class JoinSettingController extends Controller
{
    public function index() 
    {
        $setting = JoinSetting::get();
        return response()->json(['status' => 'success', 'setting' => $setting]);   
    }

    public function store(JoinSettingRequest $request) 
    {   
        $data = request()->all();  

        if(JoinSetting::create($data)) {
            return response()->json(['status' => 'success', 'msg' => '新增成功！']);                             
        }

        return response()->json(['status' => 'error', 'msg' => '新增失败！']);                           
        
    }

    public function show()
    {
        $setting = JoinSetting::find(request()->setting);
        $status = $setting ? 'success' : 'error';
        return response()->json(['status' => $status, 'data' => $setting]);   
    }

    public function update(JoinSettingRequest $request)
    {
        $data = request()->all();   
             
        if(JoinSetting::where('id', request()->setting)->update($data)) {
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);                             
        }

        return response()->json(['status' => 'error', 'msg' => '更新失败！']);                            
    }

    public function destroy()
    {
        if(JoinSetting::where('id', request()->setting)->delete()) {
            return response()->json(['status' => 'success', 'msg' => '删除成功！']);                              
        }

        return response()->json(['status' => 'error', 'msg' => '删除失败！']);     
    }

}
