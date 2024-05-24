<?php
/**
 * Author david you
 * Date 2024/5/21
 * Time 20:51
 */

namespace App\Services\Qidian\traits;


use App\Services\Wechat\WechatService;


/**
 * 企业微信消息管理
 */
class MessageService extends WechatService
{
    public function __construct()
    {
        parent::__construct();
    }

}
