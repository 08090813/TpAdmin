<?php
/**
 * @Author 张超.
 * @Copyright http://www.zhangchao.name
 * @Email 416716328@qq.com
 * @DateTime 2018/3/2 11:54
 * @Desc
 */

namespace app\api\validate;
use think\Validate;
class Seller extends Validate
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