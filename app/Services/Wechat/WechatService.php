<?php
/**
 * Author david you
 * Date 2024/5/17
 * Time 16:38
 */

namespace App\Services\Wechat;

use App\Services\BaseService;
use App\Traits\Common;

/**
 * 企业微信服务业务逻辑
 * Class WechatService
 */
class WechatService extends BaseService
{
    use Common;

    public function __construct()
    {
    }

    /**
     * 获取调用凭证
     */
    public function getAccessToken()
    {
//        $work = \EasyWeChat::work(); // 企业微信
        $app = app('wechat.work');
        $a1 = $account = $app->getAccount();
        $a2 = $account->getCorpId();
        $a3 = $account->getSecret();
        $a4 = $account->getToken();
        $a5 = $account->getAesKey();
        var_dump($a1, $a2, $a3, $a4, $a5);exit();
    }
}
