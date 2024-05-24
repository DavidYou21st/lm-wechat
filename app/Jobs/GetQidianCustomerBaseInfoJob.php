<?php

namespace App\Jobs;

use App\Services\Qidian\QidianService;

/**
 * 拉取企点客户信息
 * 建议每天拉取一次
 */
class GetQidianCustomerBaseInfoJob extends Job
{

    public function __construct() {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        echo "拉取客户列表 Begin At " . date('Y-m-d H:i:s') . "\n";
        //拉取客户列表
        $service = new QidianService();
        $service->getCustList();
        echo "拉取客户列表 End At " . date('Y-m-d H:i:s') . "\n";
    }
}
