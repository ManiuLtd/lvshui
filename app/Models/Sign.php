<?php

namespace App\Models;

use App\Models\Model;

class Sign extends Model
{
    protected $table = 'fan_signs';

    public function fan()
    {
        return $this->hasOne(Fan::class,
            'id', 'fan_id');
    }

}
