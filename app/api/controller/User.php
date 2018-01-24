<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/21
 * Time: 21:44
 */

namespace app\api\controller;
use app\api\model\Users;
use think\Config;
use think\Controller;
use think\Request;
use app\common\controller\common;
use app\api\controller\Base;
use my\helper;
class User extends Controller
{
    protected $common;
    protected $help;
    protected $sign;
    public function __construct(Request $request = null)
    {
        $this->common = new common();
        $base = new Base();
        $this->help = new helper();
        parent::__construct($request);
        if (!$request->post("sign")){
            $this->common->ajaxError(401,"缺少sign签名");
        }
        //验证sign
        $this->sign=$base->verifySign($request->param(),$request->post("sign"));
        if ($this->sign!==true){
            $this->common->ajaxError(401,"sign签名错误",$this->sign);
        }
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
        if ($this->help->isCaptcha(input("post.phone",""))){
            //验证码还在有效期
            $this->common->ajaxError(400,"您的验证码还在有效期、切勿重复操作！");
        }
        //检查用户是都已经注册
        $phoneIsSet = db("users")->where("phone",input("post.phone"))->find();
        if ($phoneIsSet){
            $this->common->ajaxError(400,"您的手机号已经注册、可直接登录或找回密码！");
        }
        //生成验证码
        $capcha = (int)rand(1000,9999);
        //存入数据库
        $data['desc']="用户注册发送验证码";
        $data['captcha']=$capcha;
        $data['phone']=input("post.phone","");
        $data['date']=date("Y-m-d H:i:s");
        $sendSms = db("smsLog")->insert($data);
        if ($sendSms){
            $result = $this->common->senderSms(input("post.phone",""),array('phone'=>input("post.phone",""),'code'=>$capcha,'time'=>\config("code_exp")),'SMS_122410030');
            if ($result){
                $this->common->ajaxSuccess(200,"短信发送成功！");
            }else{
                $this->common->ajaxError(400,"短信发送失败！");
            }
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
        $user = new Users();
        $result = $user->register(Request::instance()->param());
        if ($result===true){
            $this->common->ajaxSuccess(200,"注册成功、跳转登录！");
        }else{
            $this->common->ajaxError(400,$result);
        }
    }
}