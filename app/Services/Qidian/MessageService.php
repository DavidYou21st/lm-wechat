<?php
/**
 * Author david you
 * Date 2024/5/21
 * Time 20:51
 */

namespace App\Services\Qidian\traits;

use App\Repositories\Qidian\CustomerRepository;
use App\Services\Qidian\QidianService;
use TencentQidian\App\QdMessage\Message as QdMsg;

/**
 * 消息方向
 */
const MSG_DIRECTION_B2C = "B2C";//内部联系人对外部联系人
const MSG_DIRECTION_C2B = "C2B";//外部联系人对内部联系人
const MSG_DIRECTION_B2B = "B2B";//内部联系人对内部联系人

/**
 * 企点消息管理
 */
class MessageService extends QidianService
{

    public function __construct()
    {
        parent::__construct();
    }
    /**
     * 企微消息转存到企点
     * @params array $msg 企业微信消息
     * @return bool
     */
    public function transferSave($msg)
    {
        //处理企点消息业务
        $handle = new QdMsg($this->access_token);
        //企微消息转存企点
        $response = $handle->transferSave($msg);
        if (isset($response['errcode']) && $response['errcode'] == 0) {
            return true;
        }
        #TODO 记录错误日志
        return false;
    }

}
