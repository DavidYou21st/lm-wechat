<?php
/**
 * Author david you
 * Date 2024/5/17
 * Time 16:38
 */

namespace App\Services\Wechat;

use App\Repositories\Wechat\CustomerRepository;
use App\Repositories\Wechat\FollowUserRepository;
use App\Repositories\Wechat\KefuRepository;
use App\Services\BaseService;
use Illuminate\Http\Request;
use Overtrue\LaravelWeChat\Facade;

/**
 * 企业微信业务逻辑
 * Class WorkService
 */
class WorkService extends BaseService
{
    protected $kefuRepository;
    protected $followUserRepository;
    protected $customerRepository;

    public function __construct(
        KefuRepository       $kefuRepository,
        FollowUserRepository $followUserRepository,
        CustomerRepository   $customerRepository,
    )
    {
        $this->kefuRepository = $kefuRepository;
        $this->followUserRepository = $followUserRepository;
        $this->customerRepository = $customerRepository;
    }

    /**
     * 处理企业微信的回调消息事件
     * @return string
     */
    public function event(Request $request)
    {
        $xml_str = $request->getContent();
        $this->log('收到企业微信的回调消息事件：' . $xml_str);
        $request_xml = (array)simplexml_load_string($xml_str);
        if (isset($request_xml['Event']) && ('kf_msg_or_event' == $request_xml['Event'])) {
            if ($token = $request_xml['Token'] ?? '') {
                $work = Facade::work();
                $data = $work->msg_audit->getSingleAgreeStatus($info);
            }
        }
        return 'success';
    }

    public function getAccessToken()
    {
        $app = Facade::work('default');
        $data = $app->access_token->getToken();
        var_dump($data);
        exit();
    }

    /**
     * 拉取企业微信会话记录
     */
    public function getWorkMsg()
    {
        $work = Facade::work();
        $limit = 10;
//        $data = $work->kf_message->sync($cursor, $token, $limit);

//        var_dump($data);exit();
    }

    public function getPermitUsers()
    {
        $app = Facade::work('chatdata_sync_msg');
        $data = $app->msg_audit->getPermitUsers();
        var_dump($data);
        exit();
    }

    /**
     * 获取企业微信客服列表
     * @return void
     */
    public function getKefuList()
    {
        $app = Facade::work('user_detail');
        $data = $app->kf_account->list();
        var_dump($data);exit();
        if (0 == $data['errcode']) {
            $corp_id = config('wechat.work.default.corp_id');
            $this->kefuRepository->add($data['account_list'] ?? [], $corp_id);
        }
    }

    /**
     * 获取配置了客户联系功能的成员列表
     * @return void
     */
    public function getFollowUsers()
    {
        $app = Facade::work('user_detail');
        $data = $app->external_contact->getFollowUsers();
        if (0 == $data['errcode']) {
            $corp_id = config('wechat.work.default.corp_id');
            $this->followUserRepository->add($data['follow_user'] ?? [], $corp_id);
        }
    }

    /**
     * 获取企业微信客户列表
     * @return void
     */
    public function getCustomers()
    {
        $app = Facade::work('user_detail');
        //获取所有接待人员
        $follow_users = $this->followUserRepository->getAll();
        $corp_id = config('wechat.work.default.corp_id');
        foreach ($follow_users as $item) {
            //查询接待人员添加的客户列表
            $data = $app->external_contact->list($item['user_id']);
            if (0 == $data['errcode']) {
                $this->customerRepository->add($data['external_userid'] ?? [], $corp_id);
            }
        }
    }

    /**
     * 更新企业微信客户信息
     * @return void
     */
    public function updateCustomerInfo()
    {
        $app = Facade::work('user_detail');
        $i = 0;
        //获取企业微信客户
        while ($users = $this->customerRepository->paginate(100, 'external_userid')) {
            $data = $users->toArray();
            $external_userids = array_column($data['data'] ?? [], 'external_userid');
            if (!empty($external_userids)) {
                $customer_list = $app->external_contact->batchGet($external_userids);
                var_dump($customer_list);
                exit();
                if (0 == $customer_list['errcode']) {
                    $this->customerRepository->updateInfo($customer_list['external_contact'], $item['external_userid']);
                }
            }
            $i++;
        }
        echo $i;
    }

    /**
     * 获取应用的jsapi_ticket
     * @return string
     */
    public function getAgentTicket()
    {
        $app = Facade::work('default');
        $agent_id = config('wechat.work.default.agent_id');
        $data = $app->jssdk->getAgentTicket($agent_id);
        return $data['ticket'] ?? '';
    }

    /**
     * 获取企业的jsapi_ticket
     * @return string
     */
    public function getEnterpriseTicket()
    {
        $app = Facade::work('default');
        $data = $app->jssdk->getTicket();
        return $data['ticket'] ?? '';
    }
}
