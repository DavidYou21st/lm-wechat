<?php

namespace App\Http\Controllers\Qidian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Qidian\QidianService;

/**
 * 腾讯企点控制器
 * Class EventController
 */
class QidianController extends Controller
{
    private $service;

    public function __construct(QidianService $service)
    {
        $this->service = $service;
    }

    /**
     * 处理腾讯企点的服务器消息
     * @see https://api.qidian.qq.com/wiki/doc/open/emxmpkwd5soewhkky37i
     * @param Request $request
     * @return string
     */
    public function serve(Request $request)
    {
        $resp = $this->service->serve($request);
        return response($resp);
    }

    /**
     * 处理腾讯企点的事件推送消息
     * @see https://api.qidian.qq.com/wiki/doc/open/epko939s7aq8br19gz0i
     * @param Request $request
     * @return string
     */
    public function event(Request $request)
    {
        $resp = $this->service->event($request);
        return response($resp);
    }

    /**
     * 腾讯企点应用授权请求（指令回调URL）
     * @see https://api.qidian.qq.com/wiki/doc/open/enudsepks7pq90r54frh
     * @param Request $request
     * @return string
     */
    public function appAuth(Request $request)
    {
        $resp = $this->service->appAuth($request);
        return response($resp);
    }

    /**
     * 给腾讯企点推送消息
     * @return string
     */
    public function push()
    {
        $resp = [];
        return response($resp);
    }
}
