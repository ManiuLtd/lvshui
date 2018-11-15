<?php
/**
 * Created by PhpStorm.
 * User: 29673
 * Date: 2018/11/15
 * Time: 15:51
 */

namespace App\Models;
use App\Models\Model;

class Club extends Model
{
    protected $table = 'clubs';

    public function fans()
    {
        return $this->belongsToMany(Fan::class,'fan_clubs',
            'club_id','fan_id')
            ->withPivot(['fan_id','club_id']);
    }
}