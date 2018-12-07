<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $table = 'orders';
    protected $guarded=[];

    public function goods()
    {
        return $this->hasManyThrough(MallGood::class,OrderGood::class,'order_id','id','id','good_id');
    }

    public function active()
    {
        return $this->hasManyThrough(Activity::class,OrderGood::class,'order_id','id','id','good_id');
    }

    public function join()
    {
        return $this->hasManyThrough(JoinSetting::class,OrderGood::class,'order_id','id','id','good_id');
    }

    public function setting()
    {
        return $this->hasOne(OrderSetting::class,'id','end_id');
    }

}
