<?php

namespace App\Models;

class Fan extends Model
{
    public static function getByOpenID($openid) 
    {
        return self::where('openid', $openid)->first();
    }

    public function admin()
    {
        return $this->hasOne(Admin::class,
            'fan_id', 'id');
    }
}
