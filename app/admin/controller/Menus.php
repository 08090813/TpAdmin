<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/21
 * Time: 19:22
 */

namespace app\admin\controller;
use think\controller;
use think\Request;

class Menus extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 菜单列表
     * @version 1.0.0
     * @funName menusList
     * @return  Obj
     */
    public function menusList(){
        return $this->fetch();
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 左侧菜单列表
     * @version 1.0.0
     * @funName leftMenus
     * @return  Obj
     */
    public function leftMenus(){

    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 添加后台菜单
     * @version 1.0.0
     * @funName addMenus
     * @return  Obj
     */
    public function addMenus(){
        if ($this->request->isPost()){

        }else{
            return $this->fetch();
        }
    }
}