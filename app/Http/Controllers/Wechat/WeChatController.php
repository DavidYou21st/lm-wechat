<?php

namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * 微信服务控制器
 * Class WeChatController
 */
class WeChatController extends Controller
{
    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve()
    {
        #Log::info('request arrived.'); # 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志
        /**
         * 公众号服务
         * @see \EasyWeChat\OfficialAccount\Application
         */
        $app = app('wechat.official_account');
        $app->server->push(function ($message) {
            return "您好！欢迎关注我!";
        });

        return $app->server->serve();
    }
}
