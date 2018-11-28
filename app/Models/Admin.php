<?php
/**
 * Created by PhpStorm.
 * User: 29673
 * Date: 2018/11/15
 * Time: 15:51
 */

namespace App\Models;
use App\Models\Model;

class Admin extends Model
{
    protected $table = 'admins';

    public function fan()
    {
        return $this->hasOne(Fan::class,
            'id', 'fan_id');
    }
}