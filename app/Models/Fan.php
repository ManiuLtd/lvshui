<?php

namespace App\Models;

class Fan extends Model
{
    public static function findUser($openid, $subscribe) 
    {
        return self::where(['openid' => $openid],['subscribe' => $subscribe])->first();
    }

    public function admin()
    {
        return $this->hasOne(Admin::class,
            'fan_id', 'id');
    }
}
