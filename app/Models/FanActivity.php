<?php

namespace App\Models;

use App\Models\Model;

class FanActivity extends Model
{
    protected $table = 'fan_activitys';
    protected $guarded=[];

    public function activity()
    {
        return $this->belongsTo(Activity::class,'activity_id','id');
    }
}
