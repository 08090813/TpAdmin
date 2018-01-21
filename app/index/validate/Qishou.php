<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/8
 * Time: 18:56
 */
namespace app\index\validate;
use think\Validate;
class Qishou extends Validate
{
    protected $rule = [
        'phone'  =>  'require',
        'password' =>  'require',
    ];
    protected $message = [
        'name.require' => '用户名不能为空！',
        'password.require' => '密码不能为空！'
    ];
}