<?php

namespace App\Services;

use EasyWeChat\Factory;

class TemplateNotice 
{
    private $app;

    public function __construct() 
    {
        $this->app = Factory::officialAccount(config('wechat.official_account.default'));
    }

    public function sendNotice(string $openid, string $template_id, string $url, array $data) 
    {
        $this->app->template_message->send([
            'touser' => $openid,
            'template_id' => $template_id,
            'url' => $url,
            'data' => $data
        ]);
    }

}