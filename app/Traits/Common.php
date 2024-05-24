<?php
/**
 * Author david you
 * Date 2024/5/22
 * Time 18:30
 */

namespace App\Traits;

use Illuminate\Support\Facades\Log;

/**
 * 通用日志打印
 */
trait Common
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
