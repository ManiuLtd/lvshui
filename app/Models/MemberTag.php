<?php

namespace App\Models;

use App\Models\Model;

class MemberTag extends Model
{
    protected $table = 'member_tags';

    public $timestamps = false;

    public function members()
    {
        return $this->belongsToMany(Member::class, 'member_tag_links', 'tag_id', 'member_id')->withoutGlobalScopes();
    }
}
