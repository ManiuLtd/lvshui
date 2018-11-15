<?php
/**
 * Created by PhpStorm.
 * User: 29673
 * Date: 2018/11/10
 * Time: 10:55
 */

namespace App\Models;


class DiyActivity extends Model
{
    protected $table = 'diy_activitys';

    public function fans()
    {
        return $this->belongsToMany(Fan::class,'fan_diys',
            'diy_id','fan_id')
            ->withPivot(['fan_id','diy_id','name','contact_way']);
    }
}