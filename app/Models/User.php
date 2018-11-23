<?php

namespace App\Models;

use App\Models\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    
    public static function check(string $username, string $password) 
    {
        
        $userNameAttempt=compact('username','password');

        if(\Auth::guard('users')->attempt($userNameAttempt)){
            return \Auth::guard('users')->user();
        }
        return false;
    }
}
