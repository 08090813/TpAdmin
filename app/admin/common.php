<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
 * @name 对密码进行加密
 * @version 1.0.0
 * @funName passwordEncrypt
 * @param $password
 * @return  Obj
 */
function passwordEncrypt($password){
    return password_hash($password,PASSWORD_BCRYPT,array('salt'=>uuid()));
}
/**
 * 生成UUID 单机使用
 * @access public
 * @return string
 */
function uuid() {
    $charid = md5(uniqid(mt_rand(), true));
    $hyphen = chr(45);// "-"
    $uuid = chr(123)// "{"
        .substr($charid, 0, 8).$hyphen
        .substr($charid, 8, 4).$hyphen
        .substr($charid,12, 4).$hyphen
        .substr($charid,16, 4).$hyphen
        .substr($charid,20,12)
        .chr(125);// "}"
    return $uuid;
}

/**
 * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
 * @name 获取当前用户的信息
 * @version 1.0.0
 * @funName getUserInfo
 * @return  Obj
 */
function getUserInfo(){
    $userInfo = cookie("UIF");
    //去掉指定字符串的前后
    $userInfo = json_decode(base64_decode(strstr(strrchr($userInfo,"["),"]",true)),true);
    return $userInfo;
}