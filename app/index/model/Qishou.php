<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/8
 * Time: 17:35
 */
namespace app\index\model;
use think\Db;
use think\Model;
use think\Config;
class Qishou extends Model
{
    protected $table = "db_qishou";
    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 骑手注册
     * @version 1.0.0
     * @funName register
     * @return  Obj
     */
    static function register($post)
    {
        $data['password']=md5($post['password']);//加密密码
        $data['phone']=$post['phone'];
        $data['name']="大百骑手-".rand(1000,9999);
        $rider = new Qishou();
        $result = $rider->insert($data);
        if ($result){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 骑手登录
     * @version 1.0.0
     * @funName login
     * @param $data
     * @return  Obj
     */
    static function login($data){
        $rider = db("qishou")->where(array('phone'=>$data['phone'],'password'=>md5($data['password'])))->find();
        //判断是否登录成功
        if ($rider){
            return $rider;
        }else{
            return false;
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 获取骑手的个人信息
     * @version 1.0.0
     * @funName getRiderInfo
     * @param $riderId
     * @return  Obj
     */
    static function getRiderInfo($riderId){
        $riderInfo = db("qishou")->field("name,headimg,phone,money,id,health_pic,status,workstate")->where(array('id'=>$riderId))->find();
        if ($riderInfo){
            $riderInfo['health'] = $riderInfo['health_pic']?1:2;
            $riderInfo['headimg'] = $riderInfo['headimg']?$riderInfo['headimg']:Config::get("default_head_img");
            unset($riderInfo['health_pic']);
            return $riderInfo;
        }else{
            return false;
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 骑手提现
     * @version 1.0.0
     * @funName getCash
     * @return  Obj
     */
    public function getCash($data){
        //查询骑手信息
        $riderInfo = Db::table("db_qishou")->where(array('id'=>$data['rider_id'],'status'=>3))->find();
        if (!$riderInfo){
            return "身份信息不正确！";
        }
        //如果余额小于提现的金额、则提现失败
        if ($riderInfo['money']<$data['money']){
            return "提现金额不能大于您的账户余额噢！";
        }
        $insert['uid'] = $data['rider_id'];
        $insert['usertype'] = 3;
        $insert['status'] = 1;
        $insert['create_time'] = date("Y-m-d H:i:s",time());
        $insert['money'] = $data['money'];
        $insert['phone'] = $data['phone'];
        $insert['name'] = $data['name'];
        $insert['account'] = $data['account'];
        $insert['type'] = $data['type'];
        Db::startTrans();
        try{
            $result = Db::table("db_getmoney")->insert($insert);
            $money = Db::table("db_qishou")->where(array('id'=>$data['rider_id']))->setDec("money",$data['money']);
        }catch (\Exception $e){
            return "提现失败、请稍后再试！";
        }
        if ($result){
            Db::commit();
            return true;
        }else{
            Db::rollback();
            return "提现失败、请稍后再试！";
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 骑手反馈
     * @version 1.0.0
     * @funName feedBack
     * @return  Obj
     */
    public function feedBack($post){
        $result = Db::table("db_feedback")->insert($post);
        if ($result){
            return true;
        }else{
            return false;
        }
    }
}