<?php
/**
 * Author david you
 * Date 2024/5/17
 * Time 16:47
 */
namespace App\Services;

use Illuminate\Support\Facades\Validator;

class BaseService
{
    //获取验证方法
    public static function getValidate($data, $scene_name,$validate)
    {
        //数据验证
        return Validator::make($data, $validate->getSceneRules($scene_name), $validate->getSceneRulesMessages());
    }
}
