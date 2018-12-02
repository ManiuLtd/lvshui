<?php

namespace App\Http\Controllers\Api\Fans;

use App\Models\Fan;
use App\Services\Token;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\OfficialAccountToken;

class FanController extends Controller
{


    public function verifyToken() 
    {
        return response()->json(['isValid' => Token::verifyToken(request()->header('token'))]);
    }

    public function getUid()
    {
        return response()->json(['fan_id' => Token::getUid()]);
    }

    public function getUser()
    {
        $fan = Fan::with('admin')->find(Token::getUid());
        return response()->json(['data' => $fan]);
    }
}
