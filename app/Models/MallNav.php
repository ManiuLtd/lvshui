<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MallNav extends Model
{
    //
    protected $table = 'mall_navs';
    protected $guarded=[];

    public function childNav()
    {
        return $this->hasMany(MallNav::class,'sid','id');
    }

    public function allChildrenNavs(){
        return $this->childNav()->with('allChildrenNavs');
    }

}
