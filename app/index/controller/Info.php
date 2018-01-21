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
class Info extends Base
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
     * @name 获取骑手的基本信息
     * @version 1.0.0
     * @funName getRiderInfo
     * @return  Obj
     */
    public function getRiderInfo(){
        $result = Qishou::getRiderInfo($this->riderId);
        if ($result!==false){
            $this->common->ajaxSuccess(200,"获取成功！",$result);
        }else{
            $this->common->ajaxError(400,"没有注册相关账号喔！");
        }
    }
    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 骑手提现
     * @version 1.0.0
     * @funName getCash
     * @return  Obj
     */
    public function getCash(){
        $data['money'] = input("post.money",0)?input("post.money",0):0;
        $data['account'] = input("post.account",0)?input("post.account",0):0;
        $data['type'] = input("post.type",0)?input("post.money",0):0;
        $data['name'] = input("post.name",0)?input("post.money",0):0;
        $data['phone'] = input("post.phone",0)?input("post.money",0):0;
        $data['rider_id'] = $this->riderId;
        $result = $this->rider->getCash($data);
        if ($result===true){
            $this->common->ajaxSuccess(200,"提现成功、请耐心等待审核！");
        }else{
            $this->common->ajaxError(400,$result);
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 修改个人信息
     * @version 1.0.0
     * @funName updateInfo
     * @return  Obj
     */
    public function updateInfo(){
        $post = Request::instance()->param();
        if (input("post.password")){
            $post['password'] = md5(input('post.password'));
        }
        $rider = new Qishou();
        $info = $rider->allowField(true)->save($post,['id' => $this->riderId]);
        if ($info){
            $this->common->ajaxSuccess(200,"修改成功！");
        }else{
            $this->common->ajaxError(400,"修改失败了喔、请稍后再试！");
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 提交审核
     * @version 1.0.0
     * @funName putVerify
     * @return  Obj
     */
    public function putVerify(){
        //先查询骑手状态、如果已审核通过、则不允许提交
        $post = Request::instance()->param();
        $post['status'] = 2;
        $rider = new Qishou();
        $riderInfo = $rider->where(array('id'=>$this->riderId))->find();
        if ($riderInfo['status']==2){
            $this->common->ajaxError(400,"您的资料正在审核中、请耐性等待！");
        }elseif ($riderInfo['status']==3){
            $this->common->ajaxError(400,"您的资料已审核通过、请勿重复提交！");
        }
        $info = $rider->allowField(true)->save($post,['id' => $this->riderId]);
        if ($info){
            $this->common->ajaxSuccess(200,"您的资料已提交审核、请耐心等待！");
        }else{
            $this->common->ajaxError(400,"提交失败、请稍后再试吧！");
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 骑手反馈
     * @version 1.0.0
     * @funName feedBack
     * @return  Obj
     */
    public function feedBack(){
        $rider = new Qishou();
        //组装反馈参数
        $data['content'] = input("content","");
        $data['phone'] = input("phone","");
        $data['images'] = input("images","");
        $data['type'] = 3;
        $data['uid'] = $this->riderId;
        $data['uptime'] = date("Y-m-d H:i:s");
        $result = $rider->feedBack($data);
        if ($result===true){
            $this->common->ajaxSuccess(200,"您的问题已反馈、感谢您的支持！");
        }else{
            $this->common->ajaxError(400,"提交失败、请稍后再试喔！");
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 获取当前骑手已经完成的订单
     * @version 1.0.0
     * @funName getPayment
     * @return  Obj
     */
    public function getPayment(){
        $order = \app\index\model\Order::getPayment($this->riderId);
        if ($order!==false){
            //对数据进行分页处理
            $pageSize = input("post.pagesize/d",1);
            $pageNumber = input("post.pagenumber/d",15);
            $start=($pageSize-1)*$pageNumber;
            $order=array_slice($order,$start,$pageNumber);
            $this->common->ajaxSuccess(200,"获取成功！",$order);
        }else{
            $this->common->ajaxError(400,"没有更多信息了喔！");
        }
    }
}