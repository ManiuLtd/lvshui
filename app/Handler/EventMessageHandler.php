<?php 

namespace App\Handler;

use App\Models\Fan;
use EasyWeChat\Kernel\Contracts\EventHandlerInterface;

class EventMessageHandler implements EventHandlerInterface
{
    public function handle($payload = null) 
    {   
        $openid = $payload['FromUserName'];

        $createtime = $payload['CreateTime'];

        $fan = Fan::where('openid', $openid)->count();

        if($payload['Event'] == 'subscribe') {
            \Log::info($fan);
            \Log::info($payload);
            
            if($fan > 0) {
                Fan::where('openid', $openid)->update(['subscribe' => 1, 'subscribe_time' => $createtime]);
            } else {
                Fan::create(['openid'=>$openid, 'subscribe' => 1, 'subscribe_time' => $createtime]);              
            }
        }

        if($payload['Event'] == 'unsubscribe') {
            Fan::where('openid', $openid)->update(['subscribe' => 0]);
        }
    }
}