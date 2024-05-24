<?php

//全局API模块 常量定义表

//API 请求成功正常响应
defined('RETURN_SUCCESS') or define('RETURN_SUCCESS', 1);
//API 请求处理失败响应
defined('RETURN_FAIL') or define('RETURN_FAIL', 0);
//链路收集失败
defined('TRACER_COLLECTION_FAIL') or define('TRACER_COLLECTION_FAIL', 2);
//请求的IP被禁止访问
defined('REQUEST_IP_FORBIDDEN') or define('REQUEST_IP_FORBIDDEN', 403);
//请求参数错误
defined('REQUEST_PARAMETER_ERROR') or define('REQUEST_PARAMETER_ERROR', 1103);
