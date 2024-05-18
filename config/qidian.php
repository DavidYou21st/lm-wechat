<?php

return [
    /*
     * 腾讯企点配置
     */
    'default' => [
        'token' => env('TENCENT_QIDIAN_TOKEN', ''),
        'app_id' => env('TENCENT_QIDIAN_APPID', ''),
        'secret' => env('TENCENT_QIDIAN_SECRET', ''),
        'aes_key' => env('TENCENT_QIDIAN_ENCODINGAESKEY', ''),
        'sid' => env('TENCENT_QIDIAN_SID', ''),
        'built_app_secret' => env('TENCENT_QIDIAN_BUILTAPPSECRET', ''),
        'http' => [
            'timeout' => 5.0,
        ],
    ],
];
