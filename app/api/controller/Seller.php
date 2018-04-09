<?php
/**
 * @Author 张超.
 * @Copyright http://www.zhangchao.name
 * @Email 416716328@qq.com
 * @DateTime 2018/3/1 15:58
 * @Desc 处理商家的相关操作
 */

namespace app\api\controller;
use think\Controller;
use think\Request;
use app\api\model\Seller as SellerModel;
class Seller extends Base
{
    protected $sign;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        if (!$request->post("sign")){
            $this->common->ajaxError(401,"缺少sign签名");
        }
        //验证sign
        $this->sign=$this->verifySign($request->param(),$request->post("sign"));
        if ($this->sign!==true){
            $this->common->ajaxError(401,"sign签名错误",$this->sign);
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 注册成为商家
     * @version 1.0.0
     * @funName registerSeller
     * @return  Obj
     */
    public function registerSeller(){
        $phoneIsSet = db("seller")->where("phone",input("post.phone"))->find();
        if ($phoneIsSet){
            $this->common->ajaxError(400,"您的手机号已经注册、可直接登录或找回密码！");
        }
        $seller = new SellerModel;
        $result = $seller->registerSeller(Request::instance()->param());
        if ($result===true){
            $this->common->ajaxSuccess(200,"注册成功、跳转登录！");
        }else{
            $this->common->ajaxError(400,$result);
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 商家登录
     * @version 1.0.0
     * @funName doLogin
     * @return  Obj
     */
    public function Login(){
        $seller = new SellerModel();
        $result = $seller->doLogin(Request::instance()->param());
        if (is_array($result)){
            $this->common->ajaxSuccess(200,"登录成功！",$result);
        }else{
            $this->common->ajaxError(400,$result);
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 申请成为商家
     * @version 1.0.0
     * @funName applySeller
     * @return  Obj
     * @desc 身份证、使用手机号注册
     */
    public function applySeller(){
        /*
         * name 商家真实姓名
         * card_img 身份证照片
         * sex 性别
         * address 商家地址
         * auth_status 认证状态
         * birthday 出生日期
         * phone 电话号码
         * password 加密后的密码
         * money 账户余额
         */
        $result = $this->help->companyAuth("91510107MA61U5030N","四川晚自习科技有限公司","张超");
        var_dump($result);
        if (is_array($result)){

        }else{
            $this->common->ajaxError(400,$result);
        }
    }
}