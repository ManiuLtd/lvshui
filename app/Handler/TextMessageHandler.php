<?php 

namespace App\Handler;

use App\Models\Fan;
use EasyWeChat\Kernel\Contracts\EventHandlerInterface;

class TextMessageHandler implements EventHandlerInterface
{
    public function handle($payload = null) 
    {   
        return '欢迎关注，绿水清江';
    }
}