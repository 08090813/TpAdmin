<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/11
 * Time: 14:40
 */

namespace app\admin\model;


use think\Model;

class AuthGroup extends Model
{
    public function getAuthGroup($title,$pageSize,$pagenumber){
        //组装条件
        if ($title){
            $map['title'] =['like',"%{$title}%"];
        }
        $map['status'] =['eq',1];
        $result = $menus = $this->where($map)->page($pageSize,$pagenumber)->order("id desc")->select();
        $data['count'] = $this->where($map)->count();
        $data['list'] = $result;
        return $result;
    }
}