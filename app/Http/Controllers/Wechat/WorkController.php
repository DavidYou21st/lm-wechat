<?php

namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Services\Wechat\WechatService;
use App\Services\Wechat\WorkService;
use Illuminate\Http\Request;

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
        $service->getPermitUsers();
    }

    /**
     * 处理企业微信的回调消息
     *
     * @return string
     */
    public function callback(WorkService $service)
    {
        $service->callback();
    }
}
