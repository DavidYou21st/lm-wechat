<?php

namespace App\Repositories\Wechat;

use App\Repositories\BaseRepository;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * 企业微信客户
 */
class CustomerRepository extends BaseRepository
{
    public function model()
    {
        return "App\\Models\\Wechat\\WechatCustomer";
    }

    /**
     * 获取企业微信客户列表
     * @return array
     */
    public function getAll()
    {
        return $this->all()->toArray();
    }

    /**
     * 添加企业微信客户
     * @param array $data
     * @param string $corp_id
     * @return void
     * @throws ValidatorException
     */
    public function add($data, $corp_id)
    {
        if (!empty($data)) {
            $insert_data = [];
            foreach ($data as $external_userid) {
                $exits = $this->count(['external_userid' => $external_userid]);
                if (!$exits) {
                    $insert_data[] = [
                        'corp_id' => $corp_id,
                        'external_userid' => $external_userid,
                    ];
                }
            }
            if (!empty($insert_data)) {
                $this->insert($insert_data);
            }
        }
    }

    /**
     * 更新企业微信客户信息
     * @param array $data
     * @param string $id
     * @return void
     * @throws ValidatorException
     */
    public function updateInfo($data, $id)
    {
        $update_data = [
            'nickname' => $data['name'] ?? '',
            'unionid' => $data['unionid'] ?? '',
        ];
        $this->update($update_data, $id);
    }
}
