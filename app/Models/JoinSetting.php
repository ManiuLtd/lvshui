<?php

namespace App\Models;

class JoinSetting extends Model
{
    protected $table = 'member_join_settings';

    public function group()
    {
        return $this->hasOne(MemberGroup::class);
    }
    
}
