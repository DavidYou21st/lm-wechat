<?php

namespace App\Repositories\Wechat;

use App\Repositories\BaseRepository;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * 企业微信客服接待成员
 */
class FollowUserRepository extends BaseRepository
{
    public function model()
    {
        return "App\\Models\\Wechat\\WechatFollowUser";
    }

    /**
     * 获取列表
     * @return array
     */
    public function getAll()
    {
        return $this->all()->toArray();
    }

    /**
     * 添加
     * @param array $data
     * @param string $corp_id
     * @return void
     * @throws ValidatorException
     * @throws RepositoryException
     */
    public function add($data, $corp_id)
    {
        if (!empty($data)) {
            $insert_data = [];
            foreach ($data as $user_id) {
                $exits = $this->count(['user_id' => $user_id]);
                if (!$exits) {
                    $insert_data[] = [
                        'corp_id' => $corp_id,
                        'user_id' => $user_id,
                    ];
                }
            }
            if (!empty($insert_data)) {
                $this->insert($insert_data);
            }
        }
    }

}
