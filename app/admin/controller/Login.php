<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/21
 * Time: 12:04
 */

namespace app\admin\controller;
use app\admin\model\admin;
use think\Controller;
use think\captcha;
use think\Request;
use app\common\controller\common;
use think\Session;

class Login extends Controller
{
    protected $param;
    protected $captcha;
    protected $common;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->captcha = new captcha\Captcha();
        $this->common = new common();
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 后台登录页
     * @version 1.0.0
     * @funName index
     * @return  Obj
     */
    public function index(){
        return $this->fetch();
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 执行登录
     * @version 1.0.0
     * @funName doLogin
     * @return  Obj
     */
    public function doLogin(){
        $this->param = Request::instance()->post();
        //判断验证码是否正确
        if (!$this->captcha->check($this->param['code'])||!$this->param['code']){
            $this->common->ajaxError(400,"验证码不正确！");
        }
        $login = new admin();
        $result = $login->doLogin($this->param);
        if ($result !== true){
            $this->common->ajaxError(400,$result);
        }else{
            $this->common->ajaxSuccess(200,"登录成功、3秒之后自动跳转！");
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 登录时的验证码
     * @version 1.0.0
     * @funName loginCode
     * @return  Obj
     */
    public function loginCode(){
        $this->captcha->length = 3;//设置验证码长度为3位
        return $this->captcha->entry();
    }
}