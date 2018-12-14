<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class MallGoodGroup extends Model
{
    protected $table = 'mall_good_groups';
    protected $guarded=[];

    public function childGroups()
    {
        return $this->hasMany(MallGoodGroup::class,'head_id','id');
    }

    public function allChildrenGroups(){
        return $this->childGroups()->with('allChildrenGroups');
    }
    public function order()
    {
        return $this->hasOne(Order::class,'id','order_id');
    }
}
