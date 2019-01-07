<?php

namespace App\Http\Controllers\Api\Malls;

use App\Models\MallSwiper;
use App\Models\MallSwiperGroup;
use App\Utils\Parameter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MallSwiperController extends Controller
{

    public function store()
    {
        $re = request(['url', 'remake', 'display', 'image', 'group', 'type']);
        DB::beginTransaction();
        try {
            MallSwiper::create($re);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '新增失败' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '新增成功！']);
    }


    public function show()
    {
        $swiper = MallSwiper::find(request()->mall_swiper);
        if ($swiper->type == Parameter::active) {
            $swiper = $swiper->with('active')->get();
        } else if ($swiper->type == Parameter::good) {
            $swiper = $swiper->with('good')->get();
        }
        return response()->json(['data' => $swiper]);
    }

    public function update()
    {
        $re = request(['url', 'url_id', 'remake', 'display', 'image', 'group', 'type']);
        DB::beginTransaction();
        try {
            MallSwiper::where('id', request()->mall_swiper)->update($re);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '修改失败' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '修改成功！']);
    }

    public function destroy()
    {
        DB::beginTransaction();
        try {
            MallSwiper::where('id', request()->mall_swiper)->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '删除失败' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '删除成功！']);
    }


}

