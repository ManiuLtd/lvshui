<?php

namespace App\Http\Controllers\Api\Mall;

use App\Models\MallNav;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MallNavController extends Controller
{
    //
    public function index(){
        $mallNav = MallNav::where('sid',0)->with('allChildrenNavs')->get()->toArray();
        $data = $this->TreeToArray($mallNav,0);
        return $data;
    }

    public function store(){
        $request = request(['name','img_url','sid']);
        DB::beginTransaction();
        try {
            MallNav::create($request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '新增失败' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '新增成功！']);
    }

    public function update()
    {
        $list = request(['name','img_url','sid']);
        $id = request()->mallnav;
        DB::beginTransaction();
        try {
            MallNav::where('id', $id)->update($list);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '修改成功！']);
    }

    public function destroy()
    {
        $id = request()->mallnav;
        $mallNav = MallNav::where([['sid',0],['id',$id]])->with('allChildrenNavs')->first()->toArray();
        $arr = [];
        array_walk_recursive($mallNav,function ($v, $k) use(&$arr) {
            if($k == 'id')
                $arr[] = $v;
        });
        DB::beginTransaction();
        try {
            MallNav::whereIn('id',$arr)->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '删除成功！']);
    }


    public function TreeToArray($tree,$i)
    {
        $i++;
        foreach ($tree as $v) {
            $kong = '';
            for($j=1;$j<$i;$j++){
                $kong .= '-';
            }

            $v['name'] = $kong.$v['name'];
            $son = $v['all_children_navs'];
            unset($v['all_children_navs']);
            $array[] = $v;
            if(!empty($son)){
                $array = array_merge($array,$this->TreeToArray($son,$i));
            }
        }
        return $array;
    }


}
