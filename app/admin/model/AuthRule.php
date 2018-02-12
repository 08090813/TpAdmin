<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/10
 * Time: 9:16
 */

namespace app\admin\model;
use think\Model;

class AuthRule extends Model
{
    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 添加权限菜单
     * @version 1.0.0
     * @funName addMenus
     * @return  Obj
     */
    public function addMenus($data){
        //重新组装数据
        $map['title'] = $data['title'];
        $map['name'] = $data['controller']."/".$data['action'];
        $map['icon'] = $data['icon'];
        $map['is_menu'] = $data['ismenu'];
        $map['is_show'] = $data['isshow'];
        $map['pid'] = $data['pid'];
        $map['path'] = $data['path'];
        $result = $this->allowField(true)->save($map);
        return $result;
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 获取菜单列表
     * @version 1.0.0
     * @funName getMenusList
     * @param $pageSize
     * @param $pagenumber
     * @param $isMenu
     * @param $isShow
     * @return  Obj
     */
    public function getMenusList($pageSize,$pagenumber,$isMenu,$isShow,$name){
        //组装条件
        if ($isShow){
            $map['is_show'] =['eq',$isShow];
        }
        if ($isMenu){
            $map['is_menu'] =['eq',$isMenu];
        }
        if ($name){
            $map['title'] =['like',"%{$name}%"];
        }
        $map['status'] =['eq',1];
        $menus = $this->where($map)->page($pageSize,$pagenumber)->order("id desc")->select();
        //处理菜单状态
//        $isMenu = [1=>'是',2=>'否'];
//        $isShow = [1=>'显示',2=>'隐藏'];
//        foreach ($menus as $key => &$val){
//            $val['is_show'] = $isShow[$val['is_show']];
//            $val['is_menu'] = $isMenu[$val['is_menu']];
//        }
        //查询满足条件的总条数
        $data['count'] = $this->where($map)->count();
        $data['list'] = $menus;
        return $data;
    }
}