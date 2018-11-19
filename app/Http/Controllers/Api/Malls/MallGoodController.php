<?php

namespace App\Http\Controllers\Api\Malls;

use App\Models\MallGood;
use App\Models\MallGoodMallNav;
use App\Models\MallImage;
use App\Models\MallNav;
use App\Utils\Parameter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MallGoodController extends Controller
{
    //
    public function index()
    {
        $mallGoods = MallGood::with('navs')->with('imgs')->paginate(20);
        return response()->json(['data' => $mallGoods]);
    }

    public function show()
    {
        $mallGood = MallGood::where('id', request()->good)->with('navs')->with('imgs')->get();
        return response()->json(['data' => $mallGood]);
    }

    public function store()
    {
        $rGoods = request(['name', 'type', 'content', 'total', 'limit', 'price', 'discount', 'monthly_sales', 'is_up', 'sratr_date', 'end_date','nav_id']);
        $rImgs = request('imgs');

        DB::beginTransaction();
        try {
            $mallGood = MallGood::create($rGoods);
            $gid = $mallGood->id;
            foreach ($rImgs as $item) {
                MallImage::create(['good_id' => $gid, 'url' => $item]);
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
        $rGoods = request(['name', 'content', 'total', 'limit', 'price', 'discount', 'monthly_sales', 'is_up', 'sratr_date', 'end_date','nav_id']);
        $rImgs = request('imgs');
        $id = request()->good;

        DB::beginTransaction();
        try {
            MallGood::where('id', $id)->update($rGoods);
            MallImage::where('good_id', $id)->delete();

            foreach ($rImgs as $item) {
                MallImage::create(['good_id' => $id, 'url' => $item]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '修改失败' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '修改成功！']);
    }

    public function change()
    {
        $is_up =request('is_up');
        DB::beginTransaction();
        try {
            MallGood::where('id', request()->good)->update(['is_up'=>$is_up]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '修改失败' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '修改成功！']);
    }
//    public function destroy()
//    {
//        $id = request()->mall_good;
////        需加入订单判断
//        DB::beginTransaction();
//        try {
//            MallGood::whereIn('id', $id)->delete();
//            MallGoodMallNav::where('good_id',$id)->delete();
//            DB::commit();
//        } catch (\Exception $e) {
//            DB::rollBack();
//            return response()->json(['status' => 'error', 'msg' => $e]);
//        }
//        return response()->json(['status' => 'success', 'msg' => '删除成功！']);
//    }

    public function getMallHots()
    {
        $memGoods = MallGood::where('type',Parameter::member)->with('navs')->with('imgs')->orderBy('created_at','desc')->limit(4)->get();
        $disGoods = MallGood::where('type',Parameter::discount)->with('navs')->with('imgs')->orderBy('created_at','desc')->limit(4)->get();
        return response()->json(['member' => $memGoods,'discount'=> $disGoods]);
    }

    public function getMemberGoods()
    {
        $mallGoods = MallGood::where('type',Parameter::member)->with('navs')->with('imgs')->orderBy('created_at','desc')->paginate(20);
        return response()->json(['data' => $mallGoods]);
    }

    public function getDiscountGoods()
    {
        $mallGoods = MallGood::where('type',Parameter::discount)->with('navs')->with('imgs')->orderBy('created_at','desc')->paginate(20);
        return response()->json(['data' => $mallGoods]);
    }

    public function getGeneralGoods()
    {
        $mallGoods = MallGood::where('type',Parameter::general)->with('navs')->with('imgs')->orderBy('created_at','desc')->paginate(20);
        return response()->json(['data' => $mallGoods]);
    }

}
