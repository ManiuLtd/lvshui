<?php

namespace App\Http\Controllers\Api\Malls;

use App\Models\MallGood;
use App\Models\MallGoodMallNav;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MallGoodController extends Controller
{
    //
    public function index()
    {
        $mallGoods = MallGood::with('nav')->get();
        return $mallGoods ;
    }

    public function store()
    {
        $rGoods = request(['name', 'content', 'total','limit','price','discount','monthly_sales','is_up','sratr_date','end_date']);
        $rNavs = request('navs');

        DB::beginTransaction();
        try {
            $mallGood = MallGood::create($rGoods);
            $gid=$mallGood->id;
            foreach ($rNavs as $item){
                MallGoodMallNav::create(['good_id'=>$gid,'nav_id'=>$item]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '新增失败' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '新增成功！']);
    }

    public function update()
    {
        $rGoods = request(['name', 'content', 'total','limit','price','discount','monthly_sales','is_up','sratr_date','end_date']);
        $rNavs = request('navs');

        $id = request()->mallgood;

        DB::beginTransaction();
        try {
            MallGood::where('id',$id)->update($rGoods);
            MallGoodMallNav::where('good_id',$id)->delete();
            foreach ($rNavs as $item){
                MallGoodMallNav::create(['good_id'=>$id,'nav_id'=>$item]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '修改失败' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '修改成功！']);
    }

    public function destroy()
    {
        $id = request()->mallgood;
//        需加入订单判断
        DB::beginTransaction();
        try {
            MallGood::whereIn('id', $id)->delete();
            MallGoodMallNav::where('good_id',$id)->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '删除成功！']);
    }
}
