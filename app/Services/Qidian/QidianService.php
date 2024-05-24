<?php
/**
 * Author david you
 * Date 2024/5/17
 * Time 16:38
 */

namespace App\Services\Qidian;

use App\Http\Requests\Qidian\ServeRequest;
use App\Services\BaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use TencentQidian\App\Qdauthorize\Common\Common;
use TencentQidian\App\Qdauthorize\Company\CompanyService;
use TencentQidian\App\Qdauthorize\Selfbuilt\SelfBuiltService;
use TencentQidian\App\Qdbizmsgcrypt\QDMsgCrypt;

/**
 * 腾讯企点服务业务逻辑
 * Class QidianService
 */
class QidianService extends BaseService
{
    /**
     * @var string 企点自建应用token
     */
    protected string $access_token;

    // 企点接口参数规则
    private array $rules = [
        'signature' => 'required',
        'echostr' => 'string',
        'timestamp' => 'required',
        'nonce' => 'required',
        'encrypt_type' => 'string',
        'msg_signature' => 'string',
    ];

    /**
     * 腾讯企点配置
     * @var array
     * @params string $config['token'] 服务器配置令牌
     * @params string $config['aes_key'] 消息加密解密密钥
     * @params string $config['app_id'] 开发者app_id
     * @params string $config['secret'] 开发者secret
     */
    private array $config;

    public function __construct()
    {
        $config = config('qidian.default');
        $this->config = [
            'token' => $config['token'] ?? null,
            'aes_key' => $config['aes_key'] ?? null,
            'app_id' => $config['app_id'] ?? null,
            'secret' => $config['secret'] ?? null,
            'sid' => $config['sid'] ?? null,
            'built_app_id' => $config['built_app_id'] ?? null,
            'built_app_secret' => $config['built_app_secret'] ?? null,
        ];

        $this->getAccessToken();
    }

    /**
     * 处理腾讯企点的请求消息
     * @param ServeRequest $request
     * @return string
     */
    public function serve(ServeRequest $request)
    {
        if (!$request->validate()) {//参数验证不通过，返回错误
            return 'fail';
        }
        //企点加密解密工具
        $pc = new QDMsgCrypt($this->config['token'], $this->config['aes_key'], $this->config['app_id']);

        if ('GET' === $request->method()) {//完善服务器配置
            /**
             * 假设开发者填写了服务器地址URL
             * 企点那边会以GET请求的方式访问该地址，进行服务器有效性验证
             */
            $signature = $request->get('signature');//签名，用于验证Token令牌的正确性
            $echostr = $request->get('echostr');//回复字段
            $timestamp = $request->get('timestamp');//时间戳
            $nonce = $request->get('nonce');//随机数
            $replyEchoStr = '';

            $str = $pc->VerifyURL($signature, $timestamp, $nonce, $echostr, $replyEchoStr);
            if ($str == $echostr) {
                return $replyEchoStr; // 检测服务器配置操作，需要直接返回$replyEchoStr
            } else {
                return 'fail';
            }
        } else {
            /**
             *  假设企业开发者正确开启了推送服务，并注册了服务器地址
             *  企点服务器向注册的服务器地址发送post请求
             *  解析xml推送内容
             */
            $signature = $request->get('msg_signature');
            $timestamp = $request->get('timestamp');
            $encrypt_type = $request->get('encrypt_type');
            $nonce = $request->get('nonce');
            $fromXml = $request->getContent();//获取原始字符串
            $msg = '';
            $errCode = $pc->decryptMsg($signature, $timestamp, $nonce, $fromXml, $msg);
            if (0 == $errCode) {//解密成功，处理业务逻辑
                $data = simplexml_load_string($msg);
                switch ($data->MsgType) {
                    case 'text':
                        $msg = $data->Content;
                        break;
                    case 'event':
                        $msg = $data->Event;
                        break;
                    default:
                        $msg = '未知消息类型';
                        break;
                }
                return 'success';
            } else {
                $this->log($errCode, 'error');
                return 'fail';
            }
        }
    }
    /**
     * 处理腾讯企点的事件推送消息
     * @param Request $request
     * @return string
     */
    public function event(Request $request)
    {
        if (!$request->validate($this->rules)) {
            return 'fail';
        }
        //企点加密解密工具
        $pc = new QDMsgCrypt($this->config['token'], $this->config['aes_key'], $this->config['app_id']);

        $signature = $request->get('signature');//签名
        $encrypt_type = $request->get('encrypt_type');//加密类型
        $timestamp = $request->get('timestamp');//时间戳
        $nonce = $request->get('nonce');//随机数
        $fromXml = $request->getContent();//获取原始密文
        $msg = '';
        $errCode = $pc->decryptMsg($signature, $timestamp, $nonce, $fromXml, $msg);
        if (0 == $errCode) {//解密成功，处理业务逻辑
            $data = simplexml_load_string($msg);
            switch ($data->MsgType) {
                case 'text':
                    $msg = $data->Content;
                    break;
                case 'event':
                    $msg = $data->Event;
                    break;
                default:
                    $msg = '未知消息类型';
                    break;
            }
            return 'success';
        } else {
            print($errCode . "\n");
            return 'fail';
        }
    }

    /**
     * 腾讯企点应用授权请求
     * 注：开发者可以通过应用授权code换取企业的appid以及应用sid和应用授权token
     * @param Request $request
     * @return string
     */
    public function appAuth(Request $request)
    {
        if (!$request->validate($this->rules)) {
            return 'fail';
        }
        //企点加密解密工具
        $pc = new QDMsgCrypt($this->config['token'], $this->config['aes_key'], $this->config['app_id']);

        $code = $request->get('code');//应用授权code
        $state = $request->get('state');//加密类型
        $app_id = $request->get('app_id');//appid
        $sid = $request->get('sid');//企业ID
        $company_appid = $request->get('company_appid');//授权企业appid
        $fromXml = $request->getContent();//获取原始密文
        $msg = '';
        $errCode = $pc->decryptMsg($signature, $timestamp, $nonce, $fromXml, $msg);
        if (0 == $errCode) {//解密成功，处理业务逻辑
            $data = simplexml_load_string($msg);
            switch ($data->MsgType) {
                case 'text':
                    $msg = $data->Content;
                    break;
                case 'event':
                    $msg = $data->Event;
                    break;
                default:
                    $msg = '未知消息类型';
                    break;
            }
            return 'success';
        } else {
            print($errCode . "\n");
            return 'fail';
        }
    }

    /**
     * 获取调用凭证(自建应用主调流程，应用开发者调用企点的接口)
     */
    public function getAccessToken()
    {
        //先从缓存中取企点的调用凭证，如果缓存中存在，则直接返回
        $access_token =  Cache::get('qidian:access_token');
        if (!empty($access_token)) {
            $this->access_token = $access_token;
            return $this;
        }

        $object = new SelfBuiltService($this->config['token'], $this->config['aes_key'], $this->config['app_id']);
        $data = $object->getSelfBuildToken($this->config['app_id'], $this->config['sid'], $this->config['built_app_secret']);
        if (0 !== $data['code']) {//获取token失败
            # TODO 错误处理
            var_dump($data);
        }
        $expires_in = $data['data']['expires_in'] ?? 0;//过期时间戳
        $this->access_token = $data['data']['access_token'] ?? null;//获取调用凭证
        if (!empty($this->access_token)) {
            //缓存调用凭证
            Cache::put('qidian:access_token', $this->access_token, $expires_in);
        }

        return $this;
    }

    /**
     * 刷新企业授权token
     * @return void
     */
    public function refreshToken()
    {
//        $object = new CompanyService($this->config['token'], $this->config['aes_key'], $this->config['app_id']);
//        $companyRefreshTokenResult = $object->getCompanyRefreshToken(
//            $this->access_token, $this->config['app_id'], $authorizerapp_id, $authorizerRefreshToken, $sid
//        );

    }

    /**
     * 获取ticket票据
     */
    public function getTicket(Request $request)
    {
        $encryXml = $request->get('encryXml');//应用授权code
        $state = $request->get('state');//加密类型
        $app_id = $request->get('app_id');//appid
        $sid = $request->get('sid');//企业ID
        $fromXml = $request->getContent();//获取原始密文

        $encryXml = '<xml><AppId><![CDATA[202187955]]></AppId><Encrypt><![CDATA[VSq7MZqlKPgkUhcPKj6bKnAlTMSBSjIe/YEP09I84qhC4NScb6Z7/dmEFv9kUUFV3nWdIVPDO1HK36TOIceFAk9XR1iwrAjKVHFw//Y33REHmU3StpRlVxeji6/Dk2yXIhV3SetBAvwjaBgiPVJubRqlZHpmR9lsCmD1M6d/Ul69EHm13f1Su1OeY/vDy63mYIpAKv9yiAnkr/2NRx+iMnjbT7Q12N4cxDw5yfingA3wrg8xCxDqJhlxb5BtUjsKuQh2rXfbpkHwAOPCMD262B6s21lcKacUc4eJb0Adj6rLgu26C1wPe2+Yf4lZixTgiPYcBOezLf+FtXlSowvtmg==]]></Encrypt></xml>';
        $signature = '4e182dd2652bca811f86c2f04a11d65d80ae4b67';
        $timestamp = '1630374604';
        $nonce = '37381544744';
        $object = new Common($this->config['token'], $this->config['aes_key'], $this->config['app_id']);
        $ticketResult = $object->getTicket($encryXml, $signature, $timestamp, $nonce);
        return $ticketResult['ticket'] ?? '';
    }

    /**
     * 根据ticket换取应用开发商token
     *
     * @return void
     */
    public function ticket2Token()
    {
        $ticket = $this->getTicket();
        $object = new Common($this->config['token'], $this->config['aes_key'], $this->config['app_id']);
        $componentAccessTokenResult = $object->getAccessToken($this->config['app_id'], $this->config['secret'], $ticket);
        var_dump($componentAccessTokenResult); // $componentAccessToken = $componentAccessTokenResult['data']['component_access_token'];
        print("\n");
    }

    /**
     * 获取自建应用的token
     * @return void
     */
    public function getToken()
    {
        $object = new SelfBuiltService($this->config['token'], $this->config['aes_key'], $this->config['app_id']);
        $selfBuildToken = $object->getSelfBuildToken($this->config['app_id'], $this->config['sid'], $this->config['secret']);
    }
}
