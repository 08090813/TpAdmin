<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/21
 * Time: 15:05
 */
namespace app\admin\model;
use think\Model;
class admin extends Model
{
    protected $auto;
    protected $insert = [];
    protected $update = ['log_time'];

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 登录时间自动完成
     * @version 1.0.0
     * @funName setlog_timeAttr
     * @param $value
     * @return  Obj
     */
    protected function setlog_timeAttr($value){
        return date("Y-m-d H:i:s");
    }
    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 执行用户登录
     * @version 1.0.0
     * @funName doLogin
     * @return  Obj
     */
    public function doLogin($data){
        $userInfo = $this->allowField(true)->where(array('username' => ['eq',$data['username']]))->find();
        if (!$userInfo){
            return "当前用户不存在！";
        }
        if (!password_verify($data['password'],$userInfo['password'])){
            return "密码错误！";
        }else{
            //更新登录日志
            $this->updateLog($userInfo['id']);
            //设置当前用户的信息
            cookie('UIF', password_hash($userInfo,PASSWORD_BCRYPT)."=[".base64_encode(json_encode($userInfo))."="."]".password_hash($userInfo,PASSWORD_BCRYPT), 604800);//保存7天
            return true;
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 更新登录日志
     * @version 1.0.0
     * @funName updateLog
     * @return  Obj
     */
    public function updateLog($uid){
        $this->where('id',$uid)->update(array('log_time' => date("Y-m-d H:i:s")));
    }
}