<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/8
 * Time: 12:44
 */
return [
    //是否开启应用调试
    'app_debug'      => true,
    //是否使用路由
    'url_route_on'   => true,
    //是否强制使用路由
//    'url_route_must' => true,
    //腾讯云的短信配置
    'alidayu_sender' => [
        'sender_appkey'   => '24778820',
        'sender_secretKey'  => '47620b575285f032e8d1595c31dd5f1e',
        'SignName' =>'有券花'
    ],
    'baidu_map_key'      => '7pjv2qlebFh7vSXSO8GPWmfO84CLc2G8',
    'order_count' => 5,//骑手最多可接订单数
    'head_img'  => "http://rider.dabaisz.com/uploads/",
    'default_head_img'  => "http://rider.dabaisz.com/uploads/headImg.png",//骑手默认头像
    // 视图输出字符串内容替换
    'view_replace_str'       => [
        '__PUBLIC__' => '/static/public',
        '__ADMIN__' =>'/static/admin'
    ],
    'AUTH_CONFIG' =>[
        'AUTH_ON'           => true,                      // 认证开关
        'AUTH_TYPE'         => 1,                         // 认证方式，1为实时认证；2为登录认证。
        'AUTH_GROUP'        => 'auth_group',        // 用户组数据表名
        'AUTH_GROUP_ACCESS' => 'auth_group_access', // 用户-用户组关系表
        'AUTH_RULE'         => 'auth_rule',         // 权限规则表
        'AUTH_USER'         => 'member'             // 用户信息表
    ],
    'ADMIN_LIMIT' => 15
];