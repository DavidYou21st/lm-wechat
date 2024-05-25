<?php

namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Services\Wechat\MessageService;
use App\Services\Wechat\WechatService;
use App\Services\Wechat\WorkService;

/**
 * 企业微信客服控制器
 * Class WorkController
 */
class WorkController extends Controller
{
    /**
     * @var WechatService
     */
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
    public function test(WorkService $service)
    {
        $service->getKefuList();
    }

    /**
     * 接收企业微信的回调消息事件
     *
     * @return string
     */
    public function event(WorkService $service)
    {
        $service->event();
    }
}
