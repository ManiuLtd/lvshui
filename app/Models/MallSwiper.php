<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MallSwiper extends Model
{
    const active ='active';
    const good = 'good';
    const other = 'other';
    protected $table = 'mall_swipers';
    protected $guarded=[];

    public function group()
    {
        return $this->belongsTo(MallSwiperGroup::class, 'id', 'group');
    }
    public function good()
    {
        return $this->hasOne(MallGood::class,'id','url_id');
    }
    public function active()
    {
        return $this->hasOne(Activity::class,'id','url_id');
    }
}
