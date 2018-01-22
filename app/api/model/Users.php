<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/22
 * Time: 23:53
 */
namespace app\api\model;
use think\Model;
use app\api\validate;
use think\Loader;
class Users extends Model
{
    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 用户注册
     * @version 1.0.0
     * @funName register
     * @return  Obj
     */
    public function register($data){
        $result = $this->allowField(true)->insert($data);
        $validate = Loader::validate('Users');
        $msg = $validate->check($data);
        var_dump($msg);
    }
}