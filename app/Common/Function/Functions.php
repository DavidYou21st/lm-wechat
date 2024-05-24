<?php

use \Illuminate\Support\Facades\Request;

if (!function_exists('apiReturn')) {
    /**
     * 获取API返回固定格式
     * @param int|array $code
     * @param string $msg
     * @param array $data
     * @return false|string
     * @throws Exception
     */
    function apiReturn(int|array $code = RETURN_SUCCESS, string $msg = '', array $data = []): bool|string
    {
        if (is_array($code)) {
            try {
                return response()->json([
                    'code' => $code['code'],
                    'msg' => $code['msg'],
                    'data' => $code['data']
                ]);
            } catch (\Throwable $e) {
                throw new \Exception('api fail', RETURN_FAIL);
            }
        }

        return response()->json(compact('code', 'msg', 'data'));
    }
}
if (!function_exists('apiReturnError')) {
    /**
     * 获取API错误信息返回固定格式
     * @param int $code
     * @param string $msg
     * @return false|string
     * @throws Exception
     */
    function apiReturnError(int $code = RETURN_FAIL, string $msg = ''): bool|string
    {
        return apiReturn($code, $msg);
    }
}
if (!function_exists('apiReturnSuccess')) {
    /**
     * 获取API成功信息返回固定格式
     * @param int $code
     * @param string $msg
     * @return false|string
     * @throws Exception
     */
    function apiReturnSuccess(int $code = RETURN_SUCCESS, string $msg = ''): bool|string
    {
        return apiReturn($code, $msg);
    }
}

if (!function_exists('isHttp')) {
    /**
     * 是否是http[s]请求
     * @return bool
     * @throws Exception
     */
    function isHttp()
    {
        $scheme = Request::getScheme();

        if ('http' === $scheme || 'https' === $scheme) {
            return true;
        }
        return false;
    }
}
