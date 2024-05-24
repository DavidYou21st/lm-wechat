<?php

namespace App\Jobs;

use App\Services\Qidian\QidianService;
use Illuminate\Support\Facades\Log;

/**
 * 拉取企点客户列表
 * 建议每小时拉取一次
 */
class GetQidianCustomerListJob extends BaseJob
{
    private $service;

    public function __construct(QidianService $service) {
        $this->service = $service;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->log("拉取客户列表 Begin At " . date('Y-m-d H:i:s'));
        //拉取客户列表
        $this->service->getCustList();
        $this->log("拉取客户列表 End At " . date('Y-m-d H:i:s'));
    }
}
