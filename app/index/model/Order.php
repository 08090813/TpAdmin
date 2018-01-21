<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/9
 * Time: 15:32
 */
namespace app\index\model;
use think\Db;
use think\Model;
use think\Request;
use think\Config;

class Order extends Model
{
    protected $lately;
    public function __construct($data = [])
    {
        parent::__construct($data);
    }
    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 获取待接单列表
     * @version 1.0.0
     * @funName getOrders
     * @return  Obj
     */
    static function getOrders(){
        $orders = Db::table("db_order")
            ->field("si.shop_name,si.detail_address as start_address,o.cashprice,o.province,o.city,o.area,o.detail_address as end_address,o.sendtime,o.id,si.longitude,o.phone,o.name")
            ->alias("o")
            ->join("db_seller s","o.sellerid = s.id")
            ->join("db_seller_info si","si.register_phone=s.name")
            ->where(array('o.status'=>['eq',3],'o.mode'=>['eq',1]))
            ->select();
        if ($orders){
            return $orders;
        }else{
            return false;
        }
    }
    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 骑手接单
     * @version 1.0.0
     * @funName joinOrder
     * @return  Obj
     */
    static function joinOrder($orderid,$riderid){
        //接单规则 商家接单之后 3 骑手才可以接单 12
        //判断是否有订单id
        if (!$orderid){
            return "参数缺少";
        }
        //1、查询当前订单的信息
        $orderStatus = Db::table("db_order")->where(array('id'=>$orderid))->find();
        if ($orderStatus['status']!=3){
            return "订单已被别人抢走、请刷新重试！";
        }
        //一个骑手只能接5个订单
        $orderNumber = Config::get("order_count");
        $orderCount = Db::table("db_rider_order")->where(array('rider_id'=>$riderid,'status'=>1))->whereor(array('status'=>2))->count();
        if ($orderCount > $orderNumber){
            return "您已超过最大接单量！";
        }
        //加入骑手的订单
        $data['rider_id'] = $riderid;
        $data['order_id'] = $orderid;
        $data['status'] = 1;
        $data['start_time'] = time();
        $status = false;
        Db::startTrans();
        try{
            $insertOrder = Db::table("db_rider_order")->insert($data);
            //修改订单状态
            $updateOrder = Db::table("db_order")->where(array('id'=>$orderid))->setField("status",12);
            if ($insertOrder&&$updateOrder){
                $status = true;
            }else{
                $status = false;
            }
        }catch (\Exception $e){
            $status = false;
        }
        if ($status){
            Db::commit();
            return true;
        }else{
            Db::rollback();
            return "操作失败、请重试！";
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 待取货列表
     * @version 1.0.0
     * @funName getTakeOrder
     * @return  Obj
     */
    static function getTakeOrder($riderId){
        $orders = Db::table("db_rider_order")
            ->field("si.shop_name,si.detail_address as start_address,o.cashprice,o.province,o.city,o.area,o.detail_address as end_address,o.sendtime,o.id,si.longitude,o.phone,o.orderno,o.name")
            ->alias("ro")
            ->join("db_order o","o.id = ro.order_id")
            ->join("db_seller s","o.sellerid = s.id")
            ->join("db_seller_info si","s.name = si.register_phone")
            ->where(array('ro.status'=>1,'o.status'=>12,'ro.rider_id'=>$riderId))
            ->select();
        if ($orders){
            return $orders;
        }else{
            return false;
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 确认取货操作
     * @version 1.0.0
     * @funName confirmOrder
     * @return  Obj
     */
    public function confirmPickGoods($orderId,$riderId)
    {
        Db::startTrans();
        $fig = false;
        try{
            //构建子查询条件、修改订单表的状态
            $subSql = Db::table("db_rider_order")->where(array('order_id' => $orderId, 'rider_id' => $riderId))->buildSql();
            $result = Db::table("db_order")
                ->alias("o")
                ->join([$subSql => 'r'], 'o.id = r.order_id')
                ->where(array('o.id' => $orderId, 'o.status' => 5))->whereOr("o.status",3)->whereOr("o.status",12)
                ->setField("o.status", 6);
            //修改骑手订单表的状态
            $riderOrder = Db::table("db_rider_order")
                ->where(array('order_id' => $orderId, 'rider_id' =>$riderId))
                ->setField("status",2);
            if ($riderOrder&&$result){
                $fig = true;
            }else{
                $fig = false;
            }
        }catch (\Exception $e){
            return "啊哦、确认失败了哟！";
        }
        if ($fig){
            Db::commit();
            return true;
        }else{
            Db::rollback();
            return "啊哦、确认失败了哟！";
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 获取配送中的订单
     * @version 1.0.0
     * @funName distributionIng
     * @param $riderId
     * @return  Obj
     */
    public function distributionIng($riderId){
        $orders = Db::table("db_rider_order")
            ->field("si.shop_name,si.detail_address as start_address,o.cashprice,o.province,o.city,o.area,o.detail_address as end_address,o.sendtime,o.id,si.longitude,o.phone,o.orderno,o.name")
            ->alias("ro")
            ->join("db_order o","o.id = ro.order_id")
            ->join("db_seller s","o.sellerid = s.id")
            ->join("db_seller_info si","s.name = si.register_phone")
            ->where(array('ro.rider_id'=>$riderId,'ro.status'=>2,'o.status'=>['in',[6,2]]))
            ->select();
        if ($orders){
            return $orders;
        }else{
            return false;
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 确认订单送达
     * @version 1.0.0
     * @funName confirmOrder
     * @return  Obj
     */
    public function confirmOrder($orderId,$riderId){
        try{
            $result = Db::table("db_rider_order")
                ->alias("ro")
                ->where(array('ro.rider_id' => $riderId,'ro.order_id' => $orderId))
                ->setField("ro.status",3);
        }catch (\Exception $e){
            return "操作失败！";
        }
        if ($result){
            return true;
        }else{
            return "操作失败！";
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 获取订单详情
     * @version 1.0.0
     * @funName getOrderDetail
     * @param $orderId
     * @param $riderId
     * @return  Obj
     */
    public function getOrderDetail($orderId,$riderId){
        $orders = Db::table("db_rider_order")
            ->field("si.shop_name,o.cashprice,o.province,o.city,o.area,o.detail_address as end_address,o.sendtime,o.id,si.longitude,o.phone,si.logo,o.sex,o.remarks,o.name,s.id as sellerid")
            ->alias("ro")
            ->join("db_order o","o.id = ro.order_id")
            ->join("db_seller s","o.sellerid = s.id")
            ->join("db_seller_info si","s.name = si.register_phone")
            ->where(array('ro.rider_id'=>$riderId,'ro.order_id' =>$orderId))
            ->find();
        //查询订单的商品信息
        $goodsList = Db::table("db_orders_detail")
            ->field("g.images,g.goods_name,od.number,g.price,g.unit")
            ->alias("od")
            ->join("db_goods g","g.id = od.goodsid")
            ->where(array("od.orderid"=>$orders['id']))
            ->select();
        $goodsNumber = 0;//商品数量
        $total = 0.00;//商品总价
        //处理商品的图片信息
        foreach ($goodsList as $key => &$val){
            $images = explode(";",rtrim(ltrim($val['images'],";"),";"));
            $val['images'] = $images?$images[0]:"";
            $goodsNumber += $val['number'];
            $total += $val['number']*$val['price'];
        }
        $orders['goodsnumber'] = $goodsNumber;
        $orders['total'] = $total;
        $data['goodslist'] = $goodsList;
        $data['seller'] = $orders;
        if ($goodsList&&$orders){
            return $data;
        }else{
            return false;
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 获取历史订单
     * @version 1.0.0
     * @funName historyOrders
     * @return  Obj
     */
    public function historyOrders($map){
        $orders = Db::table("db_rider_order")
            ->field("si.shop_name,si.detail_address as start_address,o.cashprice,o.province,o.city,o.area,o.detail_address as end_address,o.sendtime,o.id,si.longitude,o.phone,o.orderno,ro.start_time,o.name")
            ->alias("ro")
            ->join("db_order o","o.id = ro.order_id")
            ->join("db_seller s","o.sellerid = s.id")
            ->join("db_seller_info si","s.name = si.register_phone")
            ->where($map)
            ->select();
        if ($orders){
            //处理订单时间
            foreach ($orders as $key => &$val){
                $val['sendtime'] = date("Y-m-d H:i:s",$val['start_time']);
            }
            return $orders;
        }else{
            return false;
        }
    }
    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 骑手收支记录
     * @version 1.0.0
     * @funName getPayment
     * @return  Obj
     */
    static function getPayment($riderId){
        //支出、收益

        //1、收益
        $orders = Db::table("db_rider_order")->field("o.cashprice,ro.end_time,ro.id")
            ->alias("ro")
            ->join("db_order o","o.id = ro.order_id")
            ->where(array('ro.rider_id' => $riderId,'o.status'=>['in',[7,8,9]]))
            ->order("ro.id desc")
            ->select();
        $newOrders = [];
        foreach ($orders as $key => $val){
            $newOrders[$key]['desc'] = "跑腿获得";
            $newOrders[$key]['time'] = date("Y-m-d H:i:s",$val['end_time']);
            $newOrders[$key]['price'] = "+".(string)$val['cashprice'];
        }
        $pay = Db::table("db_getmoney")
            ->field("id,money,create_time")
            ->where(array('usertype'=>3,'uid'=>$riderId))
            ->order("id desc")
            ->select();
        $newOrders1 = [];
        foreach ($pay as $key => $val){
            $newOrders1[$key]['desc'] = "提现到账";
            $newOrders1[$key]['time'] = $val['create_time'];
            $newOrders1[$key]['price'] = "-".(string)$val['money'];
        }
        $result = array_merge($newOrders,$newOrders1);
        if ($result){
            return $result;
        }else{
            return false;
        }
    }
}