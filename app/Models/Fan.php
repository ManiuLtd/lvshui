<?php

namespace App\Models;

class Fan extends Model
{
    public static function getByOpenID($openid) 
    {
        return self::where('openid', $openid)->first();
    }
}
