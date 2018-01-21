<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/8
 * Time: 12:35
 */
namespace app\common\controller;
use \Firebase\JWT\JWT;
use think\Loader;
use think\Request;
use think\config;
use tencent\Sender;
class common
{
    protected $appid = "";
    protected $appkey = "";
    public function __construct()
    {
        $this->appid = config::get("tencent_sender.sender_appid");
        $this->appkey = config::get("tencent_sender.sender_appkey");
    }
    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name token验证
     * @version 1.0.0
     * @funName isToken
     * @return  Obj
     */
    public function isToken()
    {

    }
    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 成功返回时状态
     * @version 1.0.0
     * @funName ajaxSuccess
     * @return  Obj
     */
    public function ajaxSuccess($code=200,$msg="操作成功！",$array=array())
    {
        header('Content-Type:application/json; charset=utf-8');
        $data['code']=$code;
        $data['msg']=$msg;
        if ($array){
            $data['data']=$array;
        }
        exit(json_encode($data));
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 失败返回时状态
     * @version 1.0.0
     * @funName ajaxError
     * @return  Obj
     */
    public function ajaxError($code=400,$msg="操作失败！",$array=array())
    {
        header('Content-Type:application/json; charset=utf-8');
        $data['code']=$code;
        $data['msg']=$msg;
        if ($array){
            $data['data']=$array;
        }
        exit(json_encode($data));
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 发送短信
     * @version 1.0.0
     * @funName senderSms
     * @param $phone
     * @param $templid
     * @param $params
     * @return  Obj
     */
    public function senderSms($phone,$templid,$params,$desc)
    {
        $request = Request::instance();
        $request->param();
        //发送短信注册
        //Loader::import("tencent\Sender",EXTEND_PATH);
        import("tencent.Sender");
        $sender = new Sender\SmsSingleSender($this->appid,$this->appkey);
        //传入腾讯云的短信配置参数
        $result = $sender->sendWithParam("86",$phone,$templid,$params,"","","");
        if($result){
            //组装数据
            $array['code']=$params[0];
            $array['phone']=$phone;
            $array['date']=date("Y-m-d H:i:s");
            $array['template']=$templid;
            $array['desc']=$desc;
            //记录发送的短信信息
            $isSend=db("smsLog")->insert($array);
            if($isSend){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 创建token
     * @version 1.0.0
     * @funName createToken
     * @param $data
     * @param $key
     * @param $sign
     * @return  Obj
     */
    public function createToken($data,$key)
    {
        $token = array(
            "iss" => "http://www.zhangchao.name",
            "aud" => "http://www.dabaisz.com",
            "iat" => time(),
            'exp' => strtotime("+7 day")//7天之后过期
        );
        $jwt = JWT::encode($token, $key);
        //将用户的id加入palyload
        $token['uid'] = $data;
        $jwt = JWT::encode($token, $key);
        if ($jwt){
            return $jwt;
        }else{
            return false;
        }
    }
    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 检测验证码是否过期
     * @version 1.0.0
     * @funName isCodeOver
     * @return  Obj
     */
    public function isCodeOver($phone)
    {
        $codes = db("smsLog")->where(array('phone'=>$phone))->order("id desc")->find();
        //判断短信发送时间是否大于3分钟
        $befTime = strtotime($codes['date']);
        //过期时间
        $exp_time = floor((time()-$befTime)%86400/60);//分钟
        //获取系统设置的过期时间
        $sys_exp_time = config::get("code_exp");
        if ($exp_time<$sys_exp_time){
            return $codes['code'];
        }else{
            return false;
        }
    }
}