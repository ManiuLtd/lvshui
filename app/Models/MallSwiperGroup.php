<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MallSwiperGroup extends Model
{
    protected $table = 'mall_swiper_groups';
    protected $guarded=[];

    public function swipers()
    {
        return $this->hasMany(MallSwiper::class, 'group', 'id');
    }
}

