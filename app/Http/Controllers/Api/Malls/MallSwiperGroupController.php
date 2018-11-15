<?php

namespace App\Http\Controllers\Api\Malls;

use App\Models\MallSwiper;
use App\Models\MallSwiperGroup;
use App\Utils\Parameter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MallSwiperGroupController extends Controller
{

    public function index()
    {
        $groups = MallSwiperGroup::orderBy('display', 'desc')->get();
        return response()->json(['data' => $groups]);
    }

    public function store()
    {
        $data = request()->all();
        DB::beginTransaction();
        try {
            MallSwiperGroup::create($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '新增失败' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '新增成功！']);
    }

    public function show()
    {
        $group = MallSwiperGroup::find(request()->mall_group)->with('swipers')->get();
        return response()->json(['data' => $group]);
    }

    public function update()
    {
        $data = request()->all();
        DB::beginTransaction();
        try {
            MallSwiperGroup::where('id', request()->mall_group)->update($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '修改失败' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '修改成功！']);
    }

    public function destroy()
    {
        $id = request()->mall_group;
        DB::beginTransaction();
        try {
            MallSwiperGroup::where('id', $id)->delete();
            MallSwiper::where('group', $id)->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '删除失败' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '删除成功！']);
    }

    public function getSwipers()
    {
        $swipers = MallSwiperGroup::where('display',1)->with('swipers')->get();
        return response()->json(['data' => $swipers]);
    }


}
