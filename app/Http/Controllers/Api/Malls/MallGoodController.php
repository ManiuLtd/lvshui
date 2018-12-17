<?php

namespace App\Http\Controllers\Api\Malls;

use App\Models\MallGood;
use App\Models\MallGoodMallNav;
use App\Models\MallGoodUp;
use App\Models\MallImage;
use App\Models\MallNav;
use App\Utils\Parameter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MallGoodController extends Controller
{

    public function index()
    {
        $mallGoods = MallGood::with('navs')->with('imgs')->with('up')->paginate(20);
        return response()->json(['data' => $mallGoods]);
    }

    public function show()
    {
        $mallGood = MallGood::where('id', request()->mall_good)->with('navs')->with('imgs')->with('up')->get();
        return response()->json(['data' => $mallGood]);
    }

    public function store()
    {
        $rGoods = request(['name', 'type', 'content', 'total', 'limit', 'price', 'discount', 'monthly_sales', 'sratr_date', 'end_date', 'nav_id', 'stock', 'group_num']);
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
        $rGoods = request(['name', 'content', 'type', 'total', 'limit', 'price', 'discount', 'monthly_sales', 'is_up', 'sratr_date', 'end_date', 'nav_id', 'stock', 'group_num']);
        $rImgs = request('imgs');
        $id = request()->mall_good;
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

    public function getMallHots()
    {
        $memGoods = MallGood::where('type', Parameter::member)->with('navs')->with('imgs')->orderBy('created_at', 'desc')->limit(4)->get();
        $disGoods = MallGood::where('type', Parameter::discount)->with('navs')->with('imgs')->orderBy('created_at', 'desc')->limit(4)->get();
        return response()->json(['member' => $memGoods, 'discount' => $disGoods]);
    }

    public function getMemberGoods()
    {
        $mallGoods = MallGood::where([['type', Parameter::member], ['is_up', 1]])->with('navs')->with('imgs')->orderBy('created_at', 'desc')->paginate(20);
        return response()->json(['data' => $mallGoods]);
    }

    public function getDiscountGoods()
    {
        $mallGoods = MallGood::where([['type', Parameter::discount], ['is_up', 1]])->with('navs')->with('imgs')->orderBy('created_at', 'desc')->paginate(20);
        return response()->json(['data' => $mallGoods]);
    }

    public function getGeneralGoods()
    {
        $mallGoods = MallGood::where([['type', Parameter::general], ['is_up', 1]])->with('navs')->with('imgs')->orderBy('created_at', 'desc')->paginate(20);
        return response()->json(['data' => $mallGoods]);
    }

    public function getGroupGoods()
    {
        $mallGoods = MallGood::where([['type', Parameter::group], ['is_up', 1]])->with('navs')->with('imgs')->orderBy('created_at', 'desc')->paginate(20);
        return response()->json(['data' => $mallGoods]);
    }

    public function change()
    {
        $is_up = request('is_up');
        $good_id = request()->good;
        $good = MallGood::where('id',$good_id)->first();
        DB::beginTransaction();
        try {
            MallGoodUp::where('id', $good->up_id)->update(['is_up' => 0]);
            if($is_up == 0 ){
                MallGood::where('id',$good_id)->update(['is_up'=>0]);
            }
            if ($is_up == 1) {
                $up = MallGoodUp::create(['is_up' => 1]);
                MallGood::where('id',$good_id)->update(['up_id'=>$up->id,'is_up'=>1]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '修改失败' . $e]);
        }
    }

}
