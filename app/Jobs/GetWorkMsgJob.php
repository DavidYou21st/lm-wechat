<?php

namespace App\Jobs;

use App\Services\Wechat\WorkService;

/**
 * 定时拉取企业微信会话记录
 */
class GetWorkMsgJob extends Job
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
        echo "拉取企业微信会话记录 Begin At " . date('Y-m-d H:i:s') . "\n";
        //拉取客户列表
        $service = new WorkService();
        $service->getWorkMsg();
        echo "拉取企业微信会话记录 End At " . date('Y-m-d H:i:s') . "\n";
    }
}
