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
        'username'  =>  'require',
        'password' =>  'require',
    ];

    protected $message = [
        'username'  =>  '用户名必须',
        'password' =>  '密码必填',
    ];
}