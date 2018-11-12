<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MallGood extends Model
{
    //
    protected $table = 'mail_goods';
    protected $guarded=[];

    public function nav()
    {
        return $this->hasManyThrough(MallNav::class,MallGoodMallNav::class,'good_id','id','id','nav_id');
    }

    public function img()
    {
        return $this->hasMany(MallImage::class,'good_id','id');
    }
}
