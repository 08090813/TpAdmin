<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/9
 * Time: 14:22
 */
namespace app\index\controller;
use app\common\controller\common;
use think\Controller;
use think\Request;
use app\index\model\Order as Morder;

class Order extends Base
{
    protected $header;
    protected $params;
    protected $common;
    protected $sign;
    protected $riderId;
    protected $order;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->order = new Morder();
        $this->header = Request::instance()->header();
//        var_dump($this->header);die;
        $this->params = $request->param();
        $this->common = new common();
        //验证token是否正确
        if (!$request->post("secret")||!$request->post("sign")){
            $this->common->ajaxError(403,"参数缺少或错误！");
        }
        //验证sign
        $this->sign=parent::verifySign($request->param(),$request->post("sign"));
        if ($this->sign!==true){
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
     * @name 首页
     * @version 1.0.0
     * @funName getIndexOrders
     * @return  Obj
     */
    public function getIndexOrders(){
        //获取思路 、根据当前骑手的经纬度、获取满足条件的订单
        $orders = Morder::getOrders();
        if ($orders===false){
            $this->common->ajaxError(400,"没有数据了哟！");
        }
        if (!$this->params['address']){
            $this->common->ajaxError(403,"参数address缺少！");
        }
        $newOrders = [];
        foreach ($orders as $key => &$val){
            $number = parent::distance($val['start_address'],$this->params['address']);
            if ($number<=5){
                $val['number']=$number;
                $newOrders[$key] = $val;
            }
        }
        //按照距离进行排序
        $newOrders = parent::more_sort($newOrders,"number");
        //对订单进行分页输出
        $count=count($newOrders);
        $pageSize = input("post.pagesize/d",1);
        $pageNumber =input("post.pagenumber/d",15);
        $start=($pageSize-1)*$pageNumber;
        $newOrders=array_slice($newOrders,$start,$pageNumber);
        if ($newOrders){
            $this->common->ajaxSuccess(200,"获取成功！",$newOrders);
        }else{
            $this->common->ajaxError(400,"暂无更多数据！");
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 骑手接单
     * @version 1.0.0
     * @funName joinOrder
     * @return  Obj
     */
    public function joinOrder(){
        $order = Morder::joinOrder(input("post.orderid",0),$this->riderId);
        if ($order===true){
            $this->common->ajaxSuccess(200,"成功接单、去配送吧！");
        }else{
            $this->common->ajaxError(400,$order);
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 待取货列表
     * @version 1.0.0
     * @funName getTakeOrder
     * @return  Obj
     */
    public function getTakeOrder(){
        $result = Morder::getTakeOrder($this->riderId);
        if ($result!==false){
            //分页处理
            $pageSize = input("post.pagesize/d",1);
            $pageNumber =input("post.pagenumber/d",15);
            $start=($pageSize-1)*$pageNumber;
            $result=array_slice($result,$start,$pageNumber);
            $this->common->ajaxSuccess(200,"数据获取成功！",$result);
        }else{
            $this->common->ajaxError(400,"没有更多数据了噢！");
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 获取推荐订单
     * @version 1.0.0
     * @funName getRecommendOrders
     * @return  Obj
     */
    public function getRecommendOrders(){
        //获取思路 、根据当前骑手的经纬度、获取满足条件的订单
        $orders = Morder::getOrders();
        if ($orders===false){
            $this->common->ajaxError(400,"没有数据了哟！");
        }
        if (!$this->params['address']){
            $this->common->ajaxError(403,"参数address缺少！");
        }
        $newOrders = [];
        foreach ($orders as $key => &$val){
            $number = parent::distance($val['start_address'],$this->params['address']);
            if ($number<=5){
                $val['number']=$number;
                $newOrders[$key] = $val;
            }
        }
        //按照距离进行排序
        $newOrders = parent::more_sort($newOrders,"number");
        //对订单进行分页输出
        $pageSize = input("post.pagesize/d",1);
        $pageNumber =input("post.pagenumber/d",15);
        $start=($pageSize-1)*$pageNumber;
        $newOrders=array_slice($newOrders,$start,$pageNumber);
        if ($newOrders){
            $this->common->ajaxSuccess(200,"获取成功！",$newOrders);
        }else{
            $this->common->ajaxError(400,"暂无更多数据！");
        }

    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 确认取货
     * @version 1.0.0
     * @funName confirmOrder
     * @return  Obj
     */
    public function confirmPickGoods(){
        $result = $this->order->confirmPickGoods(input("post.orderid",0),$this->riderId);
        if ($result === true){
            $this->common->ajaxSuccess(200,"确认成功、快去配送吧！");
        }else{
            $this->common->ajaxError(400,$result);
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 获取配送中的订单
     * @version 1.0.0
     * @funName distributionIng
     * @return  Obj
     */
    public function distributionIng(){
        $result = $this->order->distributionIng($this->riderId);
        if ($result!==false){
            //分页输出
            $pageSize = input("post.pagesize/d",1);
            $pageNumber =input("post.pagenumber/d",15);
            $start=($pageSize-1)*$pageNumber;
            $result=array_slice($result,$start,$pageNumber);
            $this->common->ajaxSuccess(200,"数据获取成功！",$result);
        }else{
            $this->common->ajaxError(400,"没有更多数据了噢！");
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 确认订单
     * @version 1.0.0
     * @funName confirmOrder
     * @return  Obj
     */
    public function confirmOrder(){
        $result = $this->order->confirmOrder(input("post.orderid",0),$this->riderId);
        if ($result===true){
            $this->common->ajaxSuccess(200,"订单确认成功！");
        }else{
            $this->common->ajaxError(400,$result);
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 获取订单详情
     * @version 1.0.0
     * @funName getOrderDetail
     * @return  Obj
     */
    public function getOrderDetail(){
        $result = $this->order->getOrderDetail(input("post.orderid",0),$this->riderId);
        if ($result !== false){
            $this->common->ajaxSuccess(200,"数据获取成功！",$result);
        }else{
            $this->common->ajaxError(400,"没有更多数据了哟！");
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 历史订单查询 根据条件筛选
     * @version 1.0.0
     * @funName historyOrders
     * @return  Obj
     */
    public function historyOrders(){
        //获取指定月份的订单
        $firstday = date('Y-m-01', strtotime(input("post.start_time","")));
        $lastday = strtotime(date('Y-m-d', strtotime("$firstday +1 month -1 day")));
        $firstday = strtotime($firstday);
        //1、 今日订单 7天订单 1月订单
        switch (input("post.type",1)){
            case 1:
                //2、今天开始时间
                $todayStart = mktime(0,0,0,date('m'),date('d'),date('Y'));
                $startTime = $todayStart;
                break;
            case 2:
                //3、本周开始的时间
                $weekStart = mktime(0,0,0,date('m'),date('d')-date('w')+1-7,date('Y'));
                $startTime = $weekStart;
                break;
            case 3:
                //4、本月开始时间
                $monthStart = mktime(0,0,0,date('m'),1,date('Y'));
                $startTime = $monthStart;
                break;
            default:
                $todayStart = mktime(0,0,0,date('m'),date('d'),date('Y'));
                $startTime = $todayStart;
        }
        $map['ro.rider_id']=['eq',$this->riderId];
        $map['ro.status']=['eq',3];
        if (input("start_time","")){
            $map['ro.start_time']=['between',[$firstday,$lastday]];
        }else{
            $map['ro.start_time']=['GT',$startTime];
        }
        //判断是否查询所有
        if (input("post.type",1)==4){
            $map="";
        }
        $result = $this->order->historyOrders($map);
        if ($result!==false){
            //对数据进行分页处理
            $pageSize = input("post.pagesize/d",1);
            $pageNumber = input("post.pagenumber/d",15);
            $start=($pageSize-1)*$pageNumber;
            $result=array_slice($result,$start,$pageNumber);
            $this->common->ajaxSuccess(200,"数据获取成功！",$result);
        }else{
            $this->common->ajaxError(400,"没有更多数据了哟！");
        }
    }
}