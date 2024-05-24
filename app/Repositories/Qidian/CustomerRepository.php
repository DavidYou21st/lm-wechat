<?php

namespace App\Repositories\Qidian;

use App\Repositories\BaseRepository;

/**
 * 企点客户
 */
class CustomerRepository extends BaseRepository
{
    public function model()
    {
        return "App\\Models\\Qidian\\QidianCustomer";
    }
}
