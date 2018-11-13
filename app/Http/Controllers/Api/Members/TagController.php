<?php

namespace App\Http\Controllers\Api\Members;

use App\Models\MemberTag;
use Illuminate\Http\Request;
use App\Http\Requests\TagRequest;
use App\Http\Controllers\Controller;

class TagController extends Controller
{
    public function index() 
    {
        $tags = MemberTag::get();
        return response()->json(['status' => 'success', 'data' => $tags]);   
    }

    public function store(TagRequest $request) 
    {   
        $data = request()->all();  
        if(MemberTag::create($data)) {
            return response()->json(['status' => 'success', 'msg' => '新增成功！']);                             
        }

        return response()->json(['status' => 'error', 'msg' => '新增失败！']);                           
    }

    public function show(MemberTag $memberTag)
    {
        $status = $memberTag ? 'success' : 'error';
        return response()->json(['status' => $status, 'data' => $memberTag]);   
    }

    public function update(TagRequest $request,MemberTag $memberTag)
    {
        $data = request()->all();                      
        if(MemberTag::where('id', $memberTag->id)->update($data)) {
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);                             
        }

        return response()->json(['status' => 'error', 'msg' => '更新失败！']);                            
    }

    public function destroy(MemberTag $memberTag)
    {
        if(MemberTag::where('id', $memberTag->id)->delete()) {
            return response()->json(['status' => 'success', 'msg' => '删除成功！']);                              
        }

        return response()->json(['status' => 'error', 'msg' => '删除失败！']);     
    }
    
}
