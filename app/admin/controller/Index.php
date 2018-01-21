<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/17
 * Time: 19:21
 */
namespace app\admin\controller;
use think\Controller;
use think\Config;
use think\Loader;
use tpAuth\Auth;

class Index extends Base
{
    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 首页
     * @version 1.0.0
     * @funName index
     * @return  Obj
     */
    public function index(){
//        Loader::import("tpAuth.Auth");
//        $auth = new Auth();
//        $result = $auth->check("Index/index",1);
//        var_dump(getUserInfo());die;
        return $this->fetch();
    }
    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 主页
     * @version 1.0.0
     * @funName main
     * @return  Obj
     */
    public function main(){
        return view();
    }
    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 设置
     * @version 1.0.0
     * @funName setting
     * @return  Obj
     */
    public function setting(){
        return "123";
    }
}