<?php

namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Services\Wechat\WechatService;

/**
 * 微信服务控制器
 * Class WeChatController
 */
class WeChatController extends Controller
{
    private $service;

    public function __construct(WechatService $service)
    {
        $this->service = $service;
    }

    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve()
    {
        $this->log('wechat request arrived.', 'info');

        $app = app('wechat.official_account');
        $app->server->push(function($message){
            return "欢迎关注 longmao 微信公众号！";
        });

        return $app->server->serve();
    }
}
