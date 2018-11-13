<?php
/**
 * Created by PhpStorm.
 * User: 29673
 * Date: 2018/11/10
 * Time: 11:05
 */

namespace App\Http\Requests;


class ActivityRequest extends CommonRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string',
            'places' => 'integer',
            'sign_type'=> 'integer',
            'sign_price'=>'integer',
        ];
    }
}