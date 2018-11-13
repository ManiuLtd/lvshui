<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberRecord extends Model
{
    protected $table = 'member_records';

    public $timestamps = false;
    
    public function fan() 
    {
        return $this->hasOne(Fan::class);
    }

    public function member() 
    {
        return $this->hasOne(member::class);
    }
}
