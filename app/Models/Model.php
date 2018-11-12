<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as BaseModel;


class Model extends BaseModel
{
    protected $guarded = [];
    
    public static function boot() {
        parent::boot();
    }

}
