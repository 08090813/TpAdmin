<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/11
 * Time: 14:19
 */
namespace app\admin\controller;
use app\admin\model\admin;
use app\admin\model\AuthGroup;
use app\admin\model\authGroupAccess;
use think\Controller;
use think\Db;
use think\Request;

class Auth extends Base
{
    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 权限组列表
     * @version 1.0.0
     * @funName authList
     * @return  Obj
     */
    public function authList(){
        return $this->fetch();
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 获取权限组列表
     * @version 1.0.0
     * @funName getAuthGroup
     * @return  Obj
     */
    public function getAuthGroup(){
        $group = new AuthGroup();
        $group = $group->getAuthGroup(input("get.title"),input("get.page",1),15);
        if ($group){
            $this->common->ajaxSuccess(200,"数据获取成功！",$group);
        }else{
            $this->common->ajaxError(400,"没有更多数据了哟！");
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 添加权限组
     * @version 1.0.0
     * @funName addAuth
     * @return  Obj
     */
    public function addGroup(){
        if (Request::instance()->isPost()){
            $data = input("post.formdata");
            $data = json_decode($data,true);
            $post['title'] = $data['title'];
            unset($data['title']);
            $rules = "";
            foreach ($data as $key=>$val){
                $rules.=$val.",";
            }
            $post['rules'] = $rules;
            $group = new AuthGroup();
            $result = $group->insert($post);
            if ($result){
                $this->common->ajaxSuccess(200,"添加成功！");
            }else{
                $this->common->ajaxError(400,"添加失败！");
            }
        }else{
            $menu = db("auth_rule")->select();
            //组装url
            foreach ($menu as $key=>&$val){
                $val['url'] = url($val['name']);
            }
            $menus = $this->menulist($menu);
            $this->assign("menus",$menus);
            return $this->fetch();
        }
    }
    protected function menulist($menu){
        $menus = array();
        //先找出顶级菜单
        foreach ($menu as $k => $val) {
            if($val['pid'] == 0) {
                $menus[$k] = $val;
            }
        }
        //通过顶级菜单找到下属的子菜单
        foreach ($menus as $k => $val) {
            foreach ($menu as $key => $value) {
                if($value['pid'] == $val['id']) {
                    $menus[$k]['child'][] = $value;
                }
            }
        }
        return $menus;
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 编辑、更新权限组
     * @version 1.0.0
     * @funName editGroup
     * @return  Obj
     */
    public function editGroup(){
        if (Request::instance()->isPost()){
            $data = input("post.formdata");
            $data = json_decode($data,true);
            $post['title'] = $data['title'];
            unset($data['title']);
            $rules = "";
            foreach ($data as $key=>$val){
                $rules.=$val.",";
            }
            $post['rules'] = $rules;
            $group = new AuthGroup();
            $result = $group->save($post,['id' => $data['id']]);
            if ($result){
                $this->common->ajaxSuccess(200,"更新成功！");
            }else{
                $this->common->ajaxError(400,"更新失败！");
            }
        }else{
            $id = input("get.id");
            $rule = db("auth_group")->where(array('id'=>['eq',$id]))->find();
            $rules = explode(",",$rule['rules']);
            $allRules = db("auth_rule")->select();
            foreach ($allRules as $key=>&$val){
                if (!in_array($val['id'],$rules)){
                    $val['checked'] = "";
                }else{
                    $val['checked'] = "checked";
                }
            }
            $allRules = $this->menulist($allRules);
            $this->assign("thisrule",$rules);
            $this->assign("title",$rule['title']);
            $this->assign("id",$rule['id']);
            $this->assign("allrules",$allRules);
            return $this->fetch();
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 编辑规则组名
     * @version 1.0.0
     * @funName editAuth
     * @return  Obj
     */
    public function editAuth(){
        if (Request::instance()->isPost()){
            $data = input("post.formdata");
            $data = json_decode($data,true);
            $post['title'] = $data['title'];
            $auth = new AuthGroup();
            $result = $auth->save($post,['id'=>$data['id']]);
            if ($result){
                $this->common->ajaxSuccess(200,"更新成功！");
            }else{
                $this->common->ajaxError(400,"更新失败！");
            }
        }else{
            $id = input("get.id");
            $rule = db("auth_group")->where(array('id'=>['eq',$id]))->find();
            $this->assign("rule",$rule);
            return $this->fetch();
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 删除规则组
     * @version 1.0.0
     * @funName delGroup
     * @return  Obj
     */
    public function delGroup(){
        $ids = input("get.ids","");
        $ids = explode(",",$ids);
        //组装条件
        $map['id'] = ['in',$ids];
        $auth = new AuthGroup();
        Db::startTrans();
        try{
            $resultA = $auth->where($map)->delete();
//            $access = new authGroupAccess();
//            $resultB = $access->where(array('group_id'=>['in',$ids]))->delete();
            if ($resultA){
                Db::commit();
                $this->common->ajaxSuccess(200,"删除成功！");
            }else{
                Db::rollback();
                $this->common->ajaxError(400,"删除失败！");
            }
        }
        catch(\Exception $e)
        {
            Db::rollback();
            $this->common->ajaxError(400,"删除失败！");
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 管理员列表
     * @version 1.0.0
     * @funName adminList
     * @return  Obj
     */
    public function adminList(){
        return $this->fetch();
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 添加管理员
     * @version 1.0.0
     * @funName addAdmin
     * @return  Obj
     */
    public function addAdmin(){
        if (Request::instance()->isPost()){
            Db::startTrans();
            try{
                $admin = new admin();
                $post['username'] = input("post.username","");
                $post['password'] = passwordEncrypt(input("post.password",""));
                $adminResult = $admin->allowField(true)->insertGetId($post);
                $access = new authGroupAccess();
                $map['uid'] = $adminResult;
                $map['group_id'] = input("post.group_id");
                $accessResult = $access->insert($map);
                if ($adminResult&&$accessResult){
                    Db::commit();
                    $this->common->ajaxSuccess(200,"添加成功！");
                }else{
                    Db::rollback();
                    $this->common->ajaxError(400,"添加失败！");
                }
            }catch (\Exception $e){
                Db::rollback();
            }
        }else{
            $auth = new AuthGroup();
            $group = $auth->select();
            $this->assign("group",$group);
            return $this->fetch();
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 获取管理员列表
     * @version 1.0.0
     * @funName gwtAdminList
     * @return  Obj
     */
    public function gwtAdminList(){
        $username = input("get.username","");
        $pageSize = input("get.page",1);
        $pageNumber = 15;
        $result = Db::table('zc_admin')
            ->alias('a')
            ->field("a.username,g.title,a.id")
            ->join('zc_auth_group_access ga','ga.uid = a.id')
            ->join('zc_auth_group g','ga.group_id = g.id')
            ->where(array('a.username'=>['like',"%{$username}%"]))
            ->page($pageSize,$pageNumber)
            ->select();
        $count = Db::table('zc_admin')
            ->alias('a')
            ->join('zc_auth_group_access ga','ga.uid = a.id')
            ->join('zc_auth_group g','ga.group_id = g.id')
            ->where(array('a.username'=>['like',"%{$username}%"]))
            ->count();
        $data['list'] = $result;
        $data['count'] = $count;
        if ($result){
            $this->common->LayuiAjaxSuccess(200,"数据获取成功！",$result,$count);
        }else{
            $this->common->ajaxError(400,"数据获取失败！");
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 编辑管理员
     * @version 1.0.0
     * @funName editAdmin
     * @return  Obj
     */
    public function editAdmin(){
        if (Request::instance()->isPost()){
            //更新用户信息、更新用户所属权限组
            $data['username'] = input("post.username");
            if (input("post.password")){
                $data['password'] = passwordEncrypt(input("post.password"));
            }
            Db::startTrans();
            try{
                $adminResult = Db::table("zc_admin")
                    ->where(array('id'=>['eq',input("post.id","")]))
                    ->update($data);
                $resultGroup = Db::table("zc_auth_group_access")
                    ->where(array('uid'=>['eq',input("post.id","")]))
                    ->update(array('group_id'=>input("group_id")));
                if ($resultGroup||$adminResult){
                    Db::commit();
                    $this->common->ajaxSuccess(200,"修改成功！",Request::instance()->post());
                }else{
                    Db::rollback();
                    $this->common->ajaxError(400,"修改失败！");
                }
            }catch (\Exception $e){
                Db::rollback();
                $this->common->ajaxError(400,"修改失败！");
            }
        }else{
            $id = input("get.id");
            $result = Db::table('zc_admin')
                ->alias('a')
                ->field("a.username,a.id")
                ->join('zc_auth_group_access ga','ga.uid = a.id')
                ->where(array('a.id'=>['eq',$id]))
                ->find();
            $adminGroup = db("auth_group_access")->where(array('uid'=>['eq',$id]))->find();
            $group = new AuthGroup();
            $authGroup = $group->select();
            foreach ($authGroup as $key=>&$val){
                if ($val['id'] == $adminGroup['group_id']){
                    $val['checked'] ="checked";
                }else{
                    $val['checked'] ="";
                }
            }
            $this->assign("group",$authGroup);
            $this->assign("admin",$result);
            return $this->fetch();
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 删除管理员
     * @version 1.0.0
     * @funName delAdmin
     * @return  Obj
     */
    public function delAdmin(){
        Db::startTrans();
        $adminResult = Db::table("zc_admin")
            ->where(array('id'=>['eq',input("get.ids","")]))
            ->delete();
        $resultGroup = Db::table("zc_auth_group_access")
            ->where(array('uid'=>['eq',input("get.ids","")]))
            ->delete();
        if ($resultGroup&&$adminResult){
            Db::commit();
            $this->common->ajaxSuccess(200,"删除成功！",Request::instance()->post());
        }else{
            Db::rollback();
            $this->common->ajaxError(400,"删除失败！");
        }
    }
}