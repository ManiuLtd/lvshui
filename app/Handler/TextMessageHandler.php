<?php 

namespace App\Handler;

use EasyWeChat\Kernel\Contracts\EventHandlerInterface;

class TextMessageHandler implements EventHandlerInterface
{
    public function handle($payload = null) 
    {   
        \Log::info($payload);
        
        return '欢迎关注，绿水清江';
    }
}