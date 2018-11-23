<?php
/**
 * Created by PhpStorm.
 * User: 29673
 * Date: 2018/11/15
 * Time: 15:51
 */

namespace App\Models;
use App\Models\Model;

class ShareRecords extends Model
{
    protected $table = 'share_records';

    public function share()
    {
        return $this->hasOne(Fan::class,'id','share_id');
    }

    public function beshare()
    {
        return $this->hasOne(Fan::class,'id','beshare_id');
    }

}