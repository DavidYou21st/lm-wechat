<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;

class BaseJob extends Job
{
    public function log($msg, $logLevel = 'info')
    {
        switch ($logLevel) {
            case 'info':
                Log::channel('console')->info($msg);
                break;
            case 'error':
                Log::channel('console')->error($msg);
                break;
            case 'debug':
                Log::channel('console')->debug($msg);
                break;
            default:
                Log::channel('console')->info($msg);
        }
    }
}
