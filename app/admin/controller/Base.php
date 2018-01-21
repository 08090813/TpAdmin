<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/21
 * Time: 17:02
 */

namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Loader;
use tpAuth\Auth;
use app\common\controller\common;
class Base extends Controller
{
    protected $common;
    public function __construct(Request $request = null)
    {
        $this->common = new common();
        parent::__construct($request);
        //检测用户是否登录
        if (!getUserInfo()){
            $this->redirect(url("Login/index"));
        }
        //检测是否有权限
        $request->controller();
        Loader::import("tpAuth.Auth");
        $user = getUserInfo();
        $auth = new Auth();
        $result = $auth->check($request->controller()."/".$request->action(),$user['id']);
        if (!$result){
            $this->common->ajaxError(404,"没有相应的权限！");
        }
    }
}