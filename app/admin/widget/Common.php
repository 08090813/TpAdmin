<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/11
 * Time: 13:28
 */
namespace app\admin\widget;
use app\admin\model\AuthRule;
use think\Controller;
class Common extends Controller
{
    public function menu(){
        $menu = db("auth_rule")->where(array('is_show'=>['eq',1],'is_menu'=>['eq',1]))->select();
        //组装url
        foreach ($menu as $key=>&$val){
            $val['url'] = url($val['name']);
        }
        $menus = $this->menulist($menu);
        $this->assign("menus",$menus);
        return $this->fetch('widget/menu');
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
}