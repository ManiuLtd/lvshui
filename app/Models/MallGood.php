<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MallGood extends Model
{
    //
    protected $table = 'mall_goods';
    protected $guarded=[];


    public function navs()
    {

        return $this->hasOne(MallNav::class,'id','nav_id');
    }

    public function imgs()
    {
        return $this->hasMany(MallImage::class,'good_id','id');
    }
}
