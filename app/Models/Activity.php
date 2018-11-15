<?php

namespace App\Models;


class Activity extends Model
{
    protected $table = 'activitys';

    public function fans()
    {
        return $this->belongsToMany(Fan::class,'fan_activitys',
            'activity_id','fan_id')
            ->withPivot(['fan_id','activity_id','name','contact_way']);
    }
}