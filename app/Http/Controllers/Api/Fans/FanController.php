<?php

namespace App\Http\Controllers\Api\Fans;

use App\Models\Fan;
use App\Services\Token;
use App\Http\Controllers\Controller;

class FanController extends Controller
{

    public function fans() {
        $status = request('status');
        $fans = Fan::when($status > -1, function($query) use ($status) {
            return $query->where('subscribe', $status);
        })->paginate(30);
        return response()->json(['status' => 'success','data' => $fans]);
    }

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
