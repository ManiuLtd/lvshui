<?php

namespace App\Http\Requests;

class RecordRequest extends CommonRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'fan_id' => 'required|integer',
            'coupon_id' => 'required|integer'
        ];
    }
}
