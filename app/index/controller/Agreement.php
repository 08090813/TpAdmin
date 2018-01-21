<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/9
 * Time: 9:55
 */
namespace app\index\controller;
use app\index\model\Qishou;
use think\Config;
use think\Controller;
use think\Db;
use think\Request;
use app\common\controller\common as common;
class Agreement extends Base
{
    protected $riderId;
    protected $common;
    protected $header;
    protected $sign;
    protected $rider;
    public function __construct(Request $request = null)
    {
        $this->common = new common();
        $this->rider = new Qishou();
        parent::__construct($request);
        $this->header = Request::instance()->header();
        //验证token是否正确
        if (!$request->post("secret")||!$request->post("sign")){
            $this->common->ajaxError(403,"参数缺少或错误！");
        }
        //验证sign
        $this->sign=parent::verifySign($request->param(),$request->post("sign"));
        if ($this->sign !== true){
            $this->common->ajaxError(401,"sign签名错误！",$this->sign);
        }
        if (!$this->header["token"]){
            $this->common->ajaxError(403,"参数缺少或错误！");
        }
        $token = parent::verifyToken($this->header["token"],$request->post("secret"));
        if ($token!==false){
            $token = (array)$token;
            //判断token时间是否已过期
            if ($token['exp']>time()){
                $this->riderId = $token['uid'];
            }else{
                $this->common->ajaxError(402,"token已过期");//token后期存入数据库之后、刷新token的过期时间
            }
        }else{
            $this->common->ajaxError(403,"TOKEN错误！");
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 获取协议
     * @version 1.0.0
     * @funName getAgreement
     * @return  Obj
     */
    public function getAgreement(){

    }
}