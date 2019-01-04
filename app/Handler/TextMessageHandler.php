<?php 

namespace App\Handler;

use EasyWeChat\Kernel\Contracts\EventHandlerInterface;

class TextMessageHandler implements EventHandlerInterface
{
    public function handle($payload = null) 
    {   
        return config('wechat.retMsg');
    }
}