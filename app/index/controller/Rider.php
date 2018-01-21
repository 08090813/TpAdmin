<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/8
 * Time: 12:40
 */
namespace app\index\controller;
use app\common\controller\common as common;
use app\index\model\Qishou;
use think\Loader;
use think\config;
use think\Request;
use app\index\validate\Qishou as QishouValidate;
class Rider extends Base
{
    protected $common;
    protected $sign;
    protected $post;
    protected $riderId;
    public function __construct()
    {
        $request = Request::instance();
        $this->post = $request->param();
        $this->common = new common();
        if (!$request->post("sign")){
            $this->common->ajaxError(401,"缺少sign签名");
        }
        //验证sign
        $this->sign=parent::verifySign($request->param(),$request->post("sign"));
        if ($this->sign!==true){
            $this->common->ajaxError(401,"sign签名错误",$this->sign);
        }
    }
    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 骑手注册
     * @version 1.0.0
     * @funName register
     * @return  Obj
     */
    public function register()
    {
        $request = Request::instance();
        $post = $request->param();
        //验证sign
        if (!$this->sign){
            $this->common->ajaxError(400,"参数错误或缺少！");
        }
        $data = [
            'phone' => $post['phone'],
            'password' => $post['password'],
            'code' => $post['code']
        ];
        //判断骑手是否已经注册
        $isRider = $this->common->riderExit($data['phone']);
        if ($isRider){
            $this->common->ajaxError(400,"您已经注册过骑手、如果您已忘记密码、请使用找回密码功能！");
        }
        $codes = $this->common->isCodeOver($data['phone']);
        //判断验证码是否过期
        if (!$codes){
            $this->common->ajaxError(400,"验证码已超过有效期、请重新发送！");
        }else{
            //如果没有超过有效期、则判断验证码是否正确
            if ($codes!=$data['code']){
                $this->common->ajaxError(400,"验证码错误！");
            }
        }
        $isnull = new QishouValidate();
        $result = $isnull->check($data);
        if (!$result){
            return $this->common->ajaxError($isnull->getError());
        }
        $register = Qishou::register($data);
        if ($register){
            $this->common->ajaxSuccess(200,"恭喜您、注册成功！");
        }else{
            $this->common->ajaxError(400,"注册失败了哟、再试试吧！");
        }
    }
    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 骑手登录
     * @version 1.0.0
     * @funName login
     * @return  Obj
     */
    public function login()
    {
        $result = Qishou::login($this->post);
        //登录成功之后返回token给客户端
        if ($result !== false){
            //创建token
            $jwt = $this->common->createToken($result['id'],$this->post['secret']);
            if (!$jwt){
                $this->common->ajaxError(400,"请重试！");
            }else{
                $this->common->ajaxSuccess(200,"登陆成功！",array('token'=>$jwt));
            }
        }else{
            $this->common->ajaxError(400,"登录失败！");
        }
    }
    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 骑手发送短信
     * @version 1.0.0
     * @funName sender
     * @return  Obj
     */
    public function sender(){
        $request = Request::instance();
        $post = $request->param();
        //判断验证码是否还在有效期、如果还在有效期、则不用重新发送
        $isCodeOver = $this->common->isCodeOver($post['phone']);
        if ($isCodeOver){
            $this->common->ajaxSuccess(200,"发送成功！");
        }
        //组装短信参数
        $code = rand(1000,9999);
        //过期时间
        $exp = 10;
        $params = [$code,$exp];
        $result = $this->common->senderSms($post['phone'],42549,$params,"骑手新注册");
        if ($result){
            $this->common->ajaxSuccess(200,"发送成功！");
        }else{
            $this->common->ajaxError(400,"短信发送失败！");
        }
    }
}