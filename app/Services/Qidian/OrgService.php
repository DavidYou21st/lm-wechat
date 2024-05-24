<?php
/**
 * Author david you
 * Date 2024/5/21
 * Time 14:27
 */

namespace App\Services\Qidian\traits;

use App\Repositories\Qidian\KefuRepository;
use App\Repositories\Qidian\CustomerRepository;
use App\Repositories\Qidian\TransferOpenidRepository;
use App\Services\Qidian\QidianService;
use Prettus\Validator\Exceptions\ValidatorException;
use TencentQidian\App\QdStaff\Staff;
use TencentQidian\App\QdCustomer\Customer;

//通路类型
const OFFICIAL_ACCOUNT_CHANNEL = 1;//微信公众号通路
const WEB_IM_CHANNEL = 5;//webim通路
const MAIN_ACCOUNT_CHANNEL = 6;//主号通路
const MINI_APP_CHANNEL = 7;//微信小程序原生通路
const IMSDK_CHANNEL = 50;//imsdk通路
const WORK_PERSONAL_CHANNEL = 51;//企业微信中个人微信通路（仅提供给微信客户使用）, 默认值
const SSC_CHANNEL = 52;//ssc通路
const WORK_KEFU_CHANNEL = 53;//微信客服通路

/**
 * 通用-组织及员工管理
 */
class OrgService extends QidianService
{
    /**
     * @var KefuRepository
     */
    protected $kefuRepository;

    /**
     * @var TransferOpenidRepository
     */
    protected $transferOpenidRepository;

    public function __construct(KefuRepository $kefuRepository, TransferOpenidRepository $transferOpenidRepository)
    {
        parent::__construct();
        $this->kefuRepository = $kefuRepository;
        $this->transferOpenidRepository = $transferOpenidRepository;
    }

    /**
     * 根据账号名获取企点openId
     * @see https://api.qidian.qq.com/wiki/doc/open/e1lvod8ftkgs44hupuu3
     * @params array $accounts 账号名列表 例如：["kefu_a","kefu_b"]
     * @return bool
     * @throws ValidatorException
     */
    public function updateAccountOpenId($accounts = [])
    {
        if (empty($accounts)) {
            $list = $this->getAccounts();
            $accounts = array_column($list, 'account');
        }
        //处理企点员工客服业务
        $handle = new Staff($this->access_token);
        //根据账号名获取openId
        $response = $handle->getStaffIdByAccountBatch($accounts);
        if (isset($response['code']) && $response['code'] == 0 && isset($response['data'])) {
            $data = $response['data'];
            if (is_array($data)) {
                foreach ($data as $item) {
                    $info = $this->kefuRepository->firstOrCreate(['account' => $item['account']])->toArray();
                    $this->kefuRepository->update(['openid' => $item['openId']], $info['id']);
                }
            }
            return true;
        }
        return false;
    }

    /**
     * 获取所有企点客服帐户
     * @return array
     */
    public function getAccounts()
    {
        return $this->kefuRepository->all()->toArray();
    }

    /**
     * 企微openid换取企点openid
     * @see https://api.qidian.qq.com/wiki/doc/open/ewboe48lrpgo2qsntrh5
     * @params string $c_qw_openid 外部联系人（c侧）的企微openid
     * @params string $b_qd_openid 内部联系人（b侧）的企点openid
     * @params string $session_type 通路类型
     * @return bool
     * @throws ValidatorException
     */
    public function transferOpenid($c_qw_openid, $b_qd_openid, $session_type = WORK_PERSONAL_CHANNEL)
    {
        //处理企点客户业务
        $handle = new Customer($this->access_token);
        $response = $handle->transferOpenid($c_qw_openid, $b_qd_openid, $session_type);
        if (isset($response['code']) && $response['code'] == 0 && isset($response['data'])) {
            $response_data = $response['data'];
            $where = ['c_qw_openid' => $response_data['c_qw_openid'] ?? ''];
            $info = $this->transferOpenidRepository->firstOrCreate($where);
            if (empty($info)) {
                $data = [
                    'b_qd_openid' => $response_data['b_qd_openid'] ?? '',
                    'c_qw_openid' => $response_data['c_qw_openid'] ?? '',
                    'c_qd_openid' => $response_data['c_qd_openid'] ?? ''
                ];
                $this->transferOpenidRepository->create($data);
            } else {
                $info->b_qd_openid = $response_data['b_qd_openid'] ?? '';
                $info->c_qd_openid = $response_data['c_qd_openid'] ?? '';
                $info->save();
            }
            return true;
        }
        return false;
    }

}
