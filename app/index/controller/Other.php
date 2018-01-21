<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/11
 * Time: 17:21
 */
namespace app\index\controller;
use think\Config;
use think\Controller;
use think\Request;
use app\common\controller\common as common;
class Other{
    protected $common;
    public function __construct()
    {
        $this->common = new common();
    }
    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 骑手找回密码
     * @version 1.0.0
     * @funName upfile
     * @return  Obj
     */
    public function findPassword(){
        $request = Request::instance();
        $post = $request->param();
        //判断骑手是否已经注册
        $isRider = $this->common->riderExit($post['phone']);
        if ($isRider === false){
            $this->common->ajaxError(400,"你不是有效的骑手身份喔、要不先加入我们吧！");
        }
        $codes = $this->common->isCodeOver($post['phone']);
        //判断验证码是否过期
        if (!$codes){
            $this->common->ajaxError(400,"验证码已超过有效期、请重新发送！");
        }else{
            //如果没有超过有效期、则判断验证码是否正确
            if ($codes!=$post['code']){
                $this->common->ajaxError(400,"验证码错误！");
            }
        }
        $result = db("qishou")->where(array('id'=>['eq',$isRider['id']]))->update(array('password'=>md5($post['password'])));
        if ($result){
            $this->common->ajaxSuccess(200,"操作成功！");
        }else{
            $this->common->ajaxError(400,"操作失败！");
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 投票活动 v1
     * @version 1.0.0
     * @funName vote_v1
     * @return  Obj
     */
    public function vote_v1(){
        header("Access-Control-Allow-Origin:*");
        $request = Request::instance();
        //投票的城市、投票者ip、票数
        $data['ip'] = $request->ip();
        //根据ip判断是否已经投过票
        try{
            $votes = db("vote_v1")->where(array('ip'=>$data['ip']))->order("id desc")->find();
            //申明一个标记
            $fig = false;
            //判断最后一次投票的时间是否大于1天
            $time = time()-$votes['time'];
            $time = (int)$time/86400;
            if ($time>=1){
                $fig = true;
            }
            $data['number'] = rand(10,50);
            $data['area'] = input("post.area");
            $data['time'] = time();
            if (!$fig){
                $this->common->ajaxError(400,"您今天已经投过票了哟、明天再来吧！");
            }
            $result = db("vote_v1")->insert($data);
            if ($result){
                $this->common->ajaxSuccess(200,"您的城市已经收到您诚挚的投票！");
            }else{
                $this->common->ajaxError(400,"哎呀、程序媛姐姐身体不舒服、要不待会儿再试吧！");
            }
        }catch (\Exception $e){
            $this->common->ajaxError(400,"哎呀、程序媛姐姐身体不舒服、要不待会儿再试吧！");
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 获取投票数据
     * @version 1.0.0
     * @funName get_vote_v1
     * @return  Obj
     */
    public function get_vote_v1(){
        header("Access-Control-Allow-Origin:*");
        try{
            $result = db("vote_v1")->field("count(area) as areaCount,area,sum(number) as votes")->group("area")->select();
            //计算总票数
            $total = 0;
            $people = 0;
            foreach ($result as $key => $val){
                $total += $val['votes'];
                $people += $val['areaCount'];
                $result[$key]['svg'] = $val['votes']/$val['areaCount']*1;
            }
            $data['list'] = $result;
            $data['count'] = array('count'=>$total);
            if ($data['list']){
                $this->common->ajaxSuccess(200,"数据获取成功！",$data);
            }else{
                $this->common->ajaxError(400,"哎呀、程序媛姐姐身体不舒服、要不待会儿再试吧！");
            }
        }catch (\Exception $e){
            $this->common->ajaxError(400,"哎呀、程序媛姐姐身体不舒服、要不待会儿再试吧！");
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 获取平台协议
     * @version 1.0.0
     * @funName getAgreement
     * @return  Obj
     */
    public function getAgreement(){
        $result = db("deal")->where(array('type'=>['eq',input("post.type",0)]))->find();
        $result['content'] = htmlspecialchars_decode($result['content']);
        if ($result){
            $this->common->ajaxSuccess(200,"数据获取成功！",$result);
        }else{
            $this->common->ajaxError(400,"哎呀、程序媛姐姐身体不舒服、要不待会儿再试吧！");
        }
    }
}