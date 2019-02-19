<?php
/**
 * Created by PhpStorm.
 * User: 29673
 * Date: 2018/11/15
 * Time: 15:51
 */

namespace App\Models;

use App\Services\Token;

class Admin extends Model
{
    protected $table = 'admins';

    public function fan()
    {
        return $this->hasOne(Fan::class,
            'id', 'fan_id');
    }

    public static function isAdmin()
    {
        $flag = false;

        $admin = self::where('fan_id',Token::getUid())->first();
        if(isset($admin)) {
            $flag = true;
        } else {
            $flag = \Auth::guard('users')->id() > 0 ? true : false;
        }

        return $flag;
    }
}