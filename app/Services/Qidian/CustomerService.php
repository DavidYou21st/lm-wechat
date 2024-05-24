<?php
/**
 * Author david you
 * Date 2024/5/21
 * Time 20:51
 */

namespace App\Services\Qidian;

use App\Repositories\Qidian\CustomerRepository;
use Illuminate\Support\Facades\Cache;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;
use TencentQidian\App\QdCustomer\Customer as QdCustomer;


/**
 * 企点客户管理
 */
class CustomerService extends QidianService
{
    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        parent::__construct();
        $this->customerRepository = $customerRepository;

        $this->getAccessToken();
    }

    /**
     * 拉取客户列表
     * @return void
     * @throws RepositoryException
     */
    public function getCustList()
    {
        //先从缓存中取出此次拉取的第一个custid，即上次拉取的next_custid
        $next_custid = Cache::get('qidian:next_custid');
        $handle = new QdCustomer($this->access_token);
        $response = $handle->getCustList($next_custid);
        if (0 != $response['count']) {
            $list = $response['data']['cust_id'] ?? [];
            $customers = [];
            foreach ($list as $item) {
                $count = $this->customerRepository->count(['cust_id' => $item], 'id');
                if (empty($count)) {
                    $customers[] = ['cust_id' => $item];
                }
            }
            //批量插入企点客户
            $this->customerRepository->insert($customers);
        }
        if (!empty($response['next_custid'])) {
            //缓存调用凭证
            Cache::put('qidian:next_custid', $response['next_custid']);
        }
    }

    /**
     * 拉取客户信息
     * @params string $custid
     * @return void
     * @throws ValidatorException
     */
    public function getCustBaseInfo($custid)
    {
        $handle = new QdCustomer($this->access_token);
        $response = $handle->getCustBaseInfo($custid);
        $customer_info = [];
        $data = $response['data'] ?? [];
        //收集客户社交账号信息
        if (isset($data['socialAccount']) && !empty($data['socialAccount'])) {
            if (isset($data['socialAccount']['QQ']))
                $customer_info['qq'] = json_encode($data['socialAccount']['QQ']);
            if (isset($data['socialAccount']['qqOfficialAccountOpenid']))
                $customer_info['qq_official_account_openid'] = (int)$data['socialAccount']['qqOfficialAccountOpenid'];
            if (isset($data['socialAccount']['wxOfficialAccountOpenid']))
                $customer_info['wx_official_account_openid'] = (int)$data['socialAccount']['wxOfficialAccountOpenid'];
            if (isset($data['socialAccount']['wxSocial']))
                $customer_info['wx_social'] = json_encode($data['socialAccount']['wxSocial']);
            if (isset($data['socialAccount']['miniSocial']))
                $customer_info['mini_social'] = json_encode($data['socialAccount']['miniSocial']);
            if (isset($data['socialAccount']['wecom']))
                $customer_info['wecom'] = json_encode($data['socialAccount']['wecom']);
            if (isset($data['socialAccount']['wxkf']))
                $customer_info['wxkf'] = json_encode($data['socialAccount']['wxkf']);
            if (isset($data['socialAccount']['visitorID']))
                $customer_info['visitor_id'] = json_encode($data['socialAccount']['visitorID']);
        }
        //收集客户联系方式
        if (isset($data['contact']) && !empty($data['contact'])) {
            if (isset($data['contact']['visitorID']))
                $customer_info['wxaccount'] = $data['contact']['wxaccount'];
            if (isset($data['contact']['wxaccountInfo']))
                $customer_info['wx_account_info'] = json_encode($data['contact']['wxaccountInfo']);
        }

        if (!empty($customer_info)) {
            $info = $this->customerRepository->firstOrCreate(['cust_id' => $response['cust_id']]);
            //批量插入企点客户
            $this->customerRepository->update($customer_info, $info['id']);
        }
    }

    /**
     * 企点工作台侧边栏-用户详情
     *
     * @return int $code  0 状态码
     * @return string $msg  msg 状态信息
     * @return array  $data []
     */
    public function msg_audit()
    {
        $xml_str = file_get_contents("php://input");
        if (!empty($xml_str)) {
            //Other::logs(date("Y-m-d H:i:s").'---收到数据---'.$xml_str,"wx_work/msg_audit");
            // 解析该xml字符串，利用simpleXML
            libxml_disable_entity_loader(true);
            //禁止xml实体解析，防止xml注入
            $request_xml = simplexml_load_string($xml_str, 'SimpleXMLElement', LIBXML_NOCDATA);
            $new_xml = (array)$request_xml;

            $wx_cpt = new MsgCrypt(self::$new_token, self::$new_encodingAESKey, self::$new_corpid);
            $encrypt_data = $wx_cpt->decryptMsg($new_xml['Encrypt']);
            $encrypt_data_xml = simplexml_load_string($encrypt_data, 'SimpleXMLElement', LIBXML_NOCDATA);
            $msg_data = (array)$encrypt_data_xml;
            Other::logs(date("Y-m-d H:i:s") . '---解析数据---' . json_encode($msg_data), "wx_work/msg_audit");

            //获取会话内容存档
            //require(dirname(dirname(dirname(__DIR__))).'/vendor/wxwork/WxworkFinance.php');
            $sdk = new WxWorkFinance(self::$new_corpid, self::$new_msgauditsecret);

            $msg_id = WorkLog::get_new_msg_id();
            Other::logs(date("Y-m-d H:i:s") . '---当前从该结束ID开始---' . $msg_id['seq'], "wx_work/msg_audit");
            $seq = $msg_id['seq'] + 1;
            $wxChat = $sdk->getChatData($seq, 200);
            $chats = json_decode($wxChat, true);
            $chatRows = $chats['chatdata'];
            Other::logs(date("Y-m-d H:i:s") . '---总条数---' . count($chatRows), "wx_work/msg_audit");

            foreach ($chatRows as $val) {
                $decryptRandKey = null;
                openssl_private_decrypt(base64_decode($val['encrypt_random_key']), $decryptRandKey, self::$privateKey, OPENSSL_PKCS1_PADDING);
                $decryptChatRawContent = $sdk->decryptData($decryptRandKey, $val['encrypt_chat_msg']);

                Other::logs(date("Y-m-d H:i:s") . '---内容---' . $decryptChatRawContent, "wx_work/msg_audit");

                $j2 = json_decode($decryptChatRawContent, true);

                $is_msg = WorkLog::get_msg_id($j2['msgid']);
                if (!empty($is_msg)) {
                    continue;
                }

                if (!empty($j2['msgtype'])) {
                    $msgType = $j2['msgtype'];
                    if (in_array($msgType, ['image', 'video'])) {
                        try {
                            $suffix = $msgType == 'image' ? 'jpg' : 'mp4';
                            $fileurl = $j2[$msgType]['fileurl'] = dirname(dirname(dirname(__DIR__))) . "/uploads/wx_work/{$j2[$msgType]['md5sum']}.{$suffix}";
                            $sdk->downloadMedia($j2[$msgType]['sdkfileid'], $fileurl);
                        } catch (\Exception $e) {
                            sleep(1);
                        }
                    }
                }

                $j2['seq'] = $seq;
                WorkLog::add_msg_audit($j2);
                $seq++;
            }

        }

        // 企业微信校验 字符串
        if ($_GET && !empty($_GET['echostr'])) {
            $msg_signature = $_GET['msg_signature'];
            $timestamp = $_GET['timestamp'];
            $nonce = $_GET['nonce'];
            $echostr = $_GET['echostr'];

            $wx_cpt = new MsgCrypt(self::$new_token, self::$new_encodingAESKey, self::$new_corpid);
            $errCode = $wx_cpt->verifyUrl($msg_signature, $timestamp, $nonce, $echostr);
            if ($errCode > 500000) {
                echo $errCode;
            } else {
                print("ERR: " . $errCode . "\n\n");
                exit;
            }
        }
    }
}
