<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/8
 * Time: 13:00
 */
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;
//分组路由定义
Route::group(['method'=>"post"],function (){
    Route::post("rider/login","index/Index/index");//首页位置、自动跳转
    Route::post("rider/login","index/Rider/login");//骑手登录
    Route::post("rider/register","index/Rider/register");//骑手注册
    Route::post("rider/sender","index/Rider/sender");//骑手注册发送短信
});

Route::group(['method'=>"post"],function (){
    Route::post("info/getRiderInfo","index/Info/getRiderInfo");//获取骑手的信息
    Route::post("info/getCash","index/Info/getCash");//骑手提现
    Route::post("info/updateInfo","index/Info/updateInfo");//修改个人资料
    Route::post("info/putVerify","index/Info/putVerify");//骑手提交审核
    Route::post("info/feedBack","index/Info/feedBack");//骑手反馈信息
    Route::post("info/getPayment","index/Info/getPayment");//骑手反馈信息
});

Route::group(['method'=>"post"],function (){
    Route::post("order/getIndexOrders","index/Order/getIndexOrders");//获取首页订单信息
    Route::post("rider/joinOrder","index/Order/joinOrder");//加入接单
    Route::post("rider/getTakeOrder","index/Order/getTakeOrder");//获取待取货的订单
    Route::post("rider/getRecommendOrders","index/Order/getRecommendOrders");//获取取货时商家的周边订单
    Route::post("rider/confirmPickGoods","index/Order/confirmPickGoods");//获取取货时商家的周边订单
    Route::post("rider/distributionIng","index/Order/distributionIng");//配送中订单
    Route::post("rider/confirmOrder","index/Order/confirmOrder");//确认订单
    Route::post("rider/getOrderDetail","index/Order/getOrderDetail");//确认订单
    Route::post("rider/historyOrders","index/Order/historyOrders");//查询历史订单
});

Route::group(['method'=>"post"],function (){
    Route::post("file/upfile","index/File/upfile");//骑手上传文件
});


Route::group(['method'=>"post"],function (){
    Route::post("other/findPassword","index/Other/findPassword");//骑手上传文件
    Route::post("other/vote_v1","index/Other/vote_v1");//投票活动v1版本
    Route::post("other/get_vote_v1","index/Other/get_vote_v1");//获取投票信息
    Route::post("other/getAgreement","index/Other/getAgreement");//获取投票信息
});