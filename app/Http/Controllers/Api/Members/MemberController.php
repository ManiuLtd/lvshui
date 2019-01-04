<?php

namespace App\Http\Controllers\Api\Members;

use App\Models\Member;
use App\Models\MemberTag;
use App\Models\MemberGroup;
use Illuminate\Http\Request;
use App\Models\MemberTagLink;
use App\Http\Controllers\Controller;
use App\Http\Requests\MemberRequest;

class MemberController extends Controller
{
    public function index(Request $request) 
    {
        $keyword = $request->name;
        $tag_id = $request->tag_id;
        $members = Member::when($keyword , function($query) use ($keyword) {
            return $query->where('name', 'like', '%'.$keyword.'%')->orWhere('mobile', 'like', '%'.$keyword.'%');
        })->with(['tags' => function ($query) use ($tag_id){
            $query->when($tag_id, function($query) use ($tag_id) {
                return $query->where('tag_id', $tag_id);
            })->select('member_tags.id', 'member_tags.name');
        }])->get()->toArray();
        
        if($tag_id) {
            foreach($members as $k => &$member) {
                if(empty($member['tags'])) {
                    unset($members[$k]);
                }
            }
        }

        return response()->json(['status' => 'success', 'data' => $members]);   
    }

    public function store(MemberRequest $request) 
    {   
        $data = request()->all();   
        $data['card_id'] = time().rand(1,9);
        if(Member::create($data)) {
            return response()->json(['status' => 'success', 'msg' => '新增成功！']);                             
        }

        return response()->json(['status' => 'error', 'msg' => '新增失败！']);                           
    }

    public function show()
    {
        $member = Member::with(['tags' => function ($query){
            $query->select('member_tags.id', 'member_tags.name');
        }])->find(request()->member);
        $status = $member ? 'success' : 'error';
        return response()->json(['status' => $status, 'data' => $member]);   
    }

    public function update(MemberRequest $request)
    {
        $data = request()->all();                      
        if(Member::where('id', request()->member)->update($data)) {
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);                             
        }

        return response()->json(['status' => 'error', 'msg' => '更新失败！']);                            
    }

    public function destroy()
    {
        if(Member::where('id', request()->member)->delete()) {
            return response()->json(['status' => 'success', 'msg' => '删除成功！']);                              
        }

        return response()->json(['status' => 'error', 'msg' => '删除失败！']);     
    }

    public function group() {
        if(Member::where('id', request()->member)->update(['group_id' => request()->group_id])) {
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);                             
        }
        return response()->json(['status' => 'error', 'msg' => '更新失败！']);                            
    }

    //加入会员
    public function join(MemberRequest $request) 
    {
        $data = request()->all();   
        $data['fan_id'] = request('fan_id') ?? Token::getUid();
        $data['card_id'] = time().rand(1,9);
        $data['group_id'] = MemberGroup::default();
        if(Member::create($data)) {
            return response()->json(['status' => 'success', 'msg' => '领取成功！']);                             
        }

        return response()->json(['status' => 'error', 'msg' => '领取失败！']); 
    }

    public function changeIntegral()
    {
        $member_id = request('member_id');        
        $value = request('value');
        Member::changeIntegral($member_id, $value);
        return response()->json(['status' => 'success', 'msg' => '更新成功！']);  
    }

    public function changeMoney()
    {
        $member_id = request('member_id');
        $value = request('value');        
        Member::changeMoney($member_id, $value);
        return response()->json(['status' => 'success', 'msg' => '更新成功！']);  
    }

    public function selectTag()
    {
        $member_id = request()->member;
        $tags = Member::getNotHasTags($member_id);
        return response()->json(['status' => 'success', 'data' => $tags]);   
    }

    public function addTag()
    {
        if(MemberTagLink::create(request()->all())) {
            return response()->json(['status' => 'success', 'msg' => '添加成功！']);  
        }

        return response()->json(['status' => 'error', 'msg' => '添加失败！']);  
    }

    public function deleteTag()
    {
        if(MemberTagLink::where(request()->all())->delete()) {
            return response()->json(['status' => 'success', 'msg' => '删除成功！']);  
        }

        return response()->json(['status' => 'error', 'msg' => '删除失败！']); 
    }
}
