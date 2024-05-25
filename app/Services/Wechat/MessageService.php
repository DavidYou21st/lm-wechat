<?php
/**
 * Author david you
 * Date 2024/5/21
 * Time 20:51
 */

namespace App\Services\Qidian\traits;


namespace App\Services\Wechat;

use Overtrue\LaravelWeChat\Facade;
use Pkg6\WeWorkFinance\Provider\FFIProvider;
use Pkg6\WeWorkFinance\Provider\PHPExtProvider;
use Pkg6\WeWorkFinance\SDK;

/**
 * 企业微信消息管理
 */
class MessageService extends WechatService
{
    private $corpConfig;
    public function __construct()
    {
        parent::__construct();
        $config = config('wechat.work.chatdata_sync_msg');
        //企业配置
        $this->corpConfig = [
            'corpid'       => $config['corp_id'],
            'secret'       => $config['secret'],
            'private_keys' => $config['private_keys'],
        ];
    }

    public function getAccessToken()
    {
        $app = Facade::work('chatdata_sync_msg');
        $access_token = $app->access_token->getToken();
        var_dump($access_token);
        exit();
    }

    /**
     * 获取企业微信聊天数据
     * @return void
     */
    public function syncMsg()
    {
        $app = Facade::work('chatdata_sync_msg');
        $access_token = $app->access_token->getToken();
//        $data = $app->msg_audit->httpPost('https://qyapi.weixin.qq.com/cgi-bin/chatdata/sync_msg?access_token='.$access_token['access_token'], []);
        $data =  $app->msg_audit->getSingleAgreeStatus([['userid'=>'LuJing','exteranalopenid'=>'wmEK4yCAAAilzgBuM5KedhAyZbFO0LYg']]);
        var_dump($data);
        exit();
    }

    /**
     * 获取企业微信聊天数据
     * @return void
     */
    public function getWeChatData()
    {
        //实例化会话记录SDK
        $sdk = new SDK($this->corpConfig);
        $seq = 0;
        $limit = 100;
        //获取聊天记录
        $chatData = $sdk->getDecryptChatData($seq, $limit);
        var_dump($chatData);
        exit();
    }
}
