<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/23
 * Time: 0:04
 */
namespace app\api\validate;
use think\Validate;
class Users extends Validate
{
    protected $rule = [
        'username'  =>  'checkName|max:6',
        'password' =>  'email',
    ];

    protected $message = [
        'username'  =>  '用户名必须',
        'username.max'  =>  '用户名必须大于6位字符',
        'password' =>  '密码格式错误',
    ];

    // 自定义验证规则
    protected function checkName($value,$rule,$data)
    {
        return $rule == $value ? true : '名称错误';
    }
}