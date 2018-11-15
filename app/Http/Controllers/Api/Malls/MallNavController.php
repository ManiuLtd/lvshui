<?php

namespace App\Http\Controllers\Api\Malls;

use App\Models\MallGood;
use App\Models\MallNav;
use App\Utils\Parameter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MallNavController extends Controller
{
    //
    public function index()
    {
        $mallNav = MallNav::where('sid', 0)->with('allChildrenNavs')->get()->toArray();
        $data = $this->TreeToArray($mallNav, 0);
        return response()->json(['data' => $data]);
    }

    public function show()
    {
        $id = request()->mall_nav;
        $mallGood = MallNav::where('id',$id)->with('goods')->get();
        return response()->json(['data' => $mallGood]);
    }

    public function store()
    {
        $request = request(['name', 'img_url', 'sid']);
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
        $list = request(['name', 'img_url', 'sid']);
        $id = request()->mall_nav;
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
        $id = request()->mall_nav;
        $mallNav = MallNav::where([['sid', 0], ['id', $id]])->with('allChildrenNavs')->first()->toArray();
        $arr = [];
        array_walk_recursive($mallNav, function ($v, $k) use (&$arr) {
            if ($k == 'id')
                $arr[] = $v;
        });
        DB::beginTransaction();
        try {
            MallNav::whereIn('id', $arr)->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '删除成功！']);
    }


    public function TreeToArray($tree, $i)
    {
        $i++;
        foreach ($tree as $v) {
            $kong = '';
            for ($j = 1; $j < $i; $j++) {
                $kong .= '-';
            }

            $v['name'] = $kong . $v['name'];
            $son = $v['all_children_navs'];
            unset($v['all_children_navs']);
            $array[] = $v;
            if (!empty($son)) {
                $array = array_merge($array, $this->TreeToArray($son, $i));
            }
        }
        return $array;
    }

    public function getTopNavs()
    {

    }
    public function getSubNavs()
    {

    }


    public function getParameter()
    {
        $good = [["ch" => Parameter::ch_discount, "value" => Parameter::discount], ["ch" => Parameter::ch_general, "value" => Parameter::general], ["ch" => Parameter::ch_member, "value" => Parameter::member]];
        $swiper = [["ch" => Parameter::ch_good, "value" => Parameter::good], ["ch" => Parameter::ch_active, "value" => Parameter::active], ["ch" => Parameter::ch_other, "value" => Parameter::other]];
        return response()->json(['good' => $good,'swiper'=>$swiper]);
    }

}
