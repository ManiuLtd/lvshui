<?php

namespace App\Http\Requests;

use App\Http\Requests\CommonRequest;

class SignInRequest extends CommonRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'day' => 'required|integer',
            'reward' => 'required',
            'type'=>'required',
            'method'=>'required',
        ];
    }
}
