<?php

namespace App\Repositories\Qidian;

use Prettus\Repository\Eloquent\BaseRepository;

class TransferOpenidRepository extends BaseRepository
{
    public function model()
    {
        return "App\\Models\\Qidian\\TransferOpenid";
    }

    public function getTransferOpenid($c_qw_openid)
    {
        return $this->model->where('c_qw_openid', $c_qw_openid)->first();
    }
}
