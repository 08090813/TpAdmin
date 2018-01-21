<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/21
 * Time: 21:44
 */

namespace app\api\controller;
use think\Controller;
use think\Request;
use app\common\controller\common;
class User extends Controller
{
    protected $common;
    public function __construct(Request $request = null)
    {
        $this->common = new common();
        parent::__construct($request);
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 用户登录
     * @version 1.0.0
     * @funName login
     * @return  Obj
     */
    public function login(){
        return "123";
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 用户发送验证码
     * @version 1.0.0
     * @funName sender
     * @return  Obj
     */
    public function sender(){
        $result = $this->common->senderSms('18300856840',array('phone'=>'18300856840','code'=>'1456','time'=>3),'SMS_122410030');
        if ($result){
            $this->common->ajaxSuccess(200,"短信发送成功！");
        }else{
            $this->common->ajaxError(400,"短信发送失败！");
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 用户注册
     * @version 1.0.0
     * @funName register
     * @return  Obj
     */
    public function register(){

    }
}