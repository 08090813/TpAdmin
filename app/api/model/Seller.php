<?php
/**
 * @Author 张超.
 * @Copyright http://www.zhangchao.name
 * @Email 416716328@qq.com
 * @DateTime 2018/3/1 16:59
 * @Desc
 */

namespace app\api\model;
use app\api\validate;
use think\Loader;
use my\helper;
use think\Model;
use app\common\controller\common;

class Seller extends Model
{
    protected $help;
    public function __construct($data = [])
    {
        $this->help = new helper();
        parent::__construct($data);
    }
    public function applySeller($data){
        $result = $this->insert($data);
    }
    public function registerSeller($data){
        $users = $this->where("phone",$data['phone'])->find();
        if ($users){
            return "您已经注册过、您可以选择直接登录或找回密码！";
        }
        $captcha = $this->table("zc_sms_log")->where("phone",$data['phone'])->order("id desc")->find();
        if ($captcha['captcha']!=$data['code']||!$captcha){
            return "验证码错误！";
        }
        //判断验证码是否正确
        if (!$this->help->isCaptcha($data['phone'])){
            return "验证码已过期";
        }
        //生成用户名
        $data['username'] = "DeiQuan".date("YmdHis");
        $validate = Loader::validate('Seller');
        if (!$validate->check($data)){
            return $validate->getError();
        }
        $result = $this->allowField(true)->save($data);
        if ($result){
            return true;
        }else{
            return "系统繁忙、请稍后再试！";
        }
    }
    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 使用password_hash加密密码
     * @version 1.0.0
     * @funName setPasswordAttr
     * @return  Obj
     */
    public function setPasswordAttr($value){
        return password_hash($value,PASSWORD_BCRYPT);
    }
    public function doLogin($data){
        $result = $this->where(array('phone'=>['eq',$data['phone']]))->find();
        if (!$result){
            return "您的手机号码尚未注册！";
        }
        if (!password_verify($data['password'],$result['password'])){
            return "您输入的密码有误！";
        }
        //保存token、并返回
        $common = new common();
        $token = $common->createToken($result,$result['id']);
        $saveToken = $this->where(array('id'=>['eq',$result['id']]))->update(array('token'=>$token));
        if (!$token){
            return "Token生成错误！";
        }
        if ($token&&$result&&$saveToken){
            return array('token'=>$token);
        }else{
            return "登录失败！";
        }
    }
}