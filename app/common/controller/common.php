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
use my\helper;
use qcloud;
class common
{
    protected $secretKey = "";
    protected $appkey = "";
    protected $signName = "";
    protected $help = "";
    public function __construct()
    {
        $this->help = new  helper();
        $this->secretKey = config::get("alidayu_sender.sender_secretKey");
        $this->appkey = config::get("alidayu_sender.sender_appkey");
        $this->signName = config::get("alidayu_sender.SignName");
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
    public function senderSms($phone,$param,$tpl_code)
    {
        $request = Request::instance();
        $request->param();
        //发送短信注册
        import("alidayu.TopClient");
        import("alidayu.AlibabaAliqinFcSmsNumSendRequest");
        import("alidayu.RequestCheckUtil");
        import("alidayu.ResultSet");
        import("alidayu.TopLogger");
        define('TOP_SDK_WORK_DIR', CACHE_PATH.'sms_tmp/');
        define('TOP_SDK_DEV_MODE', false);
        $c = new \TopClient;
        $c ->appkey = $this->appkey;
        $c ->secretKey = $this->secretKey;
        $req = new \AlibabaAliqinFcSmsNumSendRequest;
        $req ->setExtend( "" );
        $req ->setSmsType( "normal" );
        $req ->setSmsFreeSignName( $this->signName);
        $req ->setSmsParam(json_encode($param));
        $req ->setRecNum( $phone );
        $req ->setSmsTemplateCode( $tpl_code );
        $resp = $c ->execute( $req );
        $resp = (array)$resp;
        if (isset($resp['result'])){
            $resp = (array)$resp['result'];
            if (isset($resp['success'])&&$resp['msg'] == 'OK'){
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
    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 获取菜单列表、
     * @version 1.0.0
     * @funName menusList
     * @return  Obj
     */
    public function menusList($ListMenus,$pid=0,$level=0){
        static $menus = [];
        foreach ($ListMenus as $key=>&$val){
            if ($val['pid'] == $pid){
                $val['level'] = substr_count($val['path'],",");
                $val['space'] = str_repeat("—",$val['level'])."L ";
                $menus[] = $val;
                $this->menusList($ListMenus,$val['id'],$level);
            }
        }
        return $menus;
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name layui请求数据时的ajax格式封装
     * @version 1.0.0
     * @funName LayuiAjaxSuccess
     * @param int $code
     * @param string $msg
     * @param int $count
     * @param $data
     * @return  Obj
     */
    public function LayuiAjaxSuccess($code=400,$msg="",$data,$count=0){
        header('Content-Type:application/json; charset=utf-8');
        $array['code']=$code;
        $array['msg']=$msg;
        $array['count']=$count;
        if ($data){
            $array['data']=$data;
        }
        exit(json_encode($array));
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name layui请求数据时的ajax格式封装
     * @version 1.0.0
     * @funName LayuiAjaxError
     * @param int $code
     * @param string $msg
     * @param int $count
     * @param $data
     * @return  Obj
     */
    public function LayuiAjaxError($code=400,$msg="",$data,$count=0){
        header('Content-Type:application/json; charset=utf-8');
        $array['code']=$code;
        $array['msg']=$msg;
        $array['count']=$count;
        if ($data){
            $array['data']=$data;
        }
        exit(json_encode($array));
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 文件上传
     * @version 1.0.0
     * @funName AppFile
     * @return  Obj
     */
    public function AppFile(){
        Loader::import("");
        $cosClient = new qcloud\Cos\Client(
            ['region' => getenv('COS_REGION'),'credentials'=>
            ['secretId'=>'AKIDyufFdvy63rTtAFpwiS6jLlyHUaimpT8f','secretKey' =>'W1PsEONNCF1kz4xdAe1DD1d4EF4h8FD2']
        ]);
        return $cosClient;
    }
}