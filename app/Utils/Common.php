<?php

namespace App\Utils;


class Common 
{
    public static function generateOrderNo() 
    {
        return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }
}