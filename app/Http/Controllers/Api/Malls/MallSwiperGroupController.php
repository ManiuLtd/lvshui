<?php

namespace App\Http\Controllers\Api\Malls;

use App\Models\MallSwiper;
use App\Models\MallSwiperGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MallSwiperGroupController extends Controller
{

    public function index()
    {
        $groups = MallSwiperGroup::orderBy('display', 'desc')->get();
        $type = [["ch" => "商品", "value" => MallSwiper::good], ["ch" => "活动", "value" => MallSwiper::active], ["ch" => "其他", "value" => MallSwiper::other]];;
        return response()->json(['type' => $type, 'groups' => $groups]);
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
        $group = MallSwiperGroup::find(request()->mallgroup)->with('swipers');
        return response()->json(['status' => success, 'data' => $group]);
    }

    public function update()
    {
        $data = request()->all();
        DB::beginTransaction();
        try {
            MallSwiperGroup::where('id', request()->mallgroup)->update($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '修改失败' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '修改成功！']);
    }

    public function destroy()
    {
        $id = request()->mallgroup;
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


}
