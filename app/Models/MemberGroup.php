<?php

namespace App\Models;

use App\Models\Member;


class MemberGroup extends Model
{
    protected $table = 'member_groups';
    
    public $timestamps = false;

    public static function getGroup() 
    {
        return MemberGroup::orderBy('value', 'asc')->get();
    }

    public static function default() 
    {
        return MemberGroup::where('default', 1)->fisrt()['id'];
    }

    public static function changeGroupLevel(int $uid) 
    {
        $member = Member::find($uid);
        $groups = self::getGroup();
        foreach($groups as $group) {
            if($member->integral_total >= $group->value) {
                $member->group_id = $group->id;
                $member->save();
            }
        }
    }
}
