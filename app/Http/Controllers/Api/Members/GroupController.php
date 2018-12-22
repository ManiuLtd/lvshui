<?php

namespace App\Http\Controllers\Api\Members;

use App\Models\MemberGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GroupController extends Controller
{
    public function index() 
    {
        $groups = MemberGroup::getGroup();
        return response()->json(['status' => 'success', 'data' => $groups]);   
    }

    public function store(GroupRequest $request) 
    {   
        $data = request()->all();  
        if(MemberGroup::create($data)) {
            return response()->json(['status' => 'success', 'msg' => '新增成功！']);                             
        }

        return response()->json(['status' => 'error', 'msg' => '新增失败！']);                           
        
    }

    public function show()
    {
        $group = MemberGroup::find(request()->group);
        $status = $group ? 'success' : 'error';
        return response()->json(['status' => $status, 'data' => $group]);   
    }

    public function update(GroupRequest $request)
    {
        $data = request()->all();                      
        if(MemberGroup::where('id', request()->group)->update($data)) {
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);                             
        }

        return response()->json(['status' => 'error', 'msg' => '更新失败！']);                            
    }

    public function destroy()
    {
        if(MemberGroup::where('id', request()->group)->delete()) {
            return response()->json(['status' => 'success', 'msg' => '删除成功！']);                              
        }

        return response()->json(['status' => 'error', 'msg' => '删除失败！']);     
    }

    //设为默认
    public function default() 
    {
        $groups = MemberGroup::get();
        foreach($groups as $group) {
            if($group->default == 1) {
                $group->default = 0;
                $group->save();
            }
        }

        if(MemberGroup::where('id', request()->group_id)->update(['default' => 1])) {
            return response()->json(['status' => 'success', 'msg' => '修改成功！']);                           
        }

        return response()->json(['status' => 'error', 'msg' => '修改失败！']); 
        
    }
}
