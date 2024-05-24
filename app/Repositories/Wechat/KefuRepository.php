<?php

namespace App\Repositories\Wechat;

use App\Repositories\BaseRepository;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * 企业微信客服
 */
class KefuRepository extends BaseRepository
{
    public function model()
    {
        return "App\\Models\\Wechat\\WechatKefu";
    }

    /**
     * 获取企业微信客服列表
     * @return array
     */
    public function getAll()
    {
        return $this->all()->toArray();
    }

    /**
     * 添加企业微信客服
     * @param array $data
     * @param string $corp_id
     * @return void
     * @throws ValidatorException
     */
    public function add($data, $corp_id)
    {
        if (!empty($data)) {
            $insert_data = [];
            foreach ($data as $item) {
                $exits = $this->count(['kf_id' => $item['open_kfid']]);
                if (!$exits) {
                    $insert_data[] = [
                        'corp_id' => $corp_id,
                        'kf_id' => $item['open_kfid'],
                        'name' => $item['name']
                    ];
                }
            }
            if (!empty($insert_data)) {
                $this->insert($insert_data);
            }
        }
    }

}
