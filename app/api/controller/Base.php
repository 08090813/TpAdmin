<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/8
 * Time: 22:03
 */
namespace app\api\controller;
use think\Controller;
use \Firebase\JWT\JWT;
use think\config;
class Base extends Controller
{
    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 验证sign和生成sign返回
     * @version 1.0.0
     * @funName verifySign
     * @param $data
     * @param $paramSign
     * @return  Obj
     */
    public function verifySign($data,$paramSign){
        //生成sign的规则：所有参数除token和sign之外其余参数、均按字典排序、拼接成字符串之后前后加上secret、MD5加密、之后转成大写

        //1、排除参数
        unset($data['token']);
        unset($data['sign']);
        $secret = $data['secret'];
        unset($data['secret']);
        ksort($data);
        $signStr = "";
        foreach ($data as $key => $val){
            $signStr.=$key.$val;
        }
        $signStr = $secret.$signStr.$secret;
        $sign = strtoupper(md5(md5($signStr)));//两次md5之后转大写
        if ($paramSign===$sign){
            return true;
        }else{
            return $sign;
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 方法注释
     * @version 1.0.0
     * @funName verifyToken
     * @param $jwt json web token
     * @param $key 开发者的唯一标识
     * @return  Obj
     */
    public function verifyToken($token,$key){
        try{
            $decoded = JWT::decode($token, $key, array('HS256'));
            return $decoded;
        }catch (\Exception $e){
            return false;
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name curl post获取
     * @version 1.0.0
     * @funName curl_post
     * @param $url
     * @param $data
     * @return  Obj
     */
    public function curl_post($url,$data){
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 1);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //设置post方式提交
        curl_setopt($curl, CURLOPT_POST, 1);
        //设置post数据
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        //执行命令
        $result = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        return $result;
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name curl get方式获取
     * @version 1.0.0
     * @funName curl_get
     * @return  Obj
     */
    public function curl_get($url){
        header("content-type:text/html;charset=utf-8");
        $curl=curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL,$url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 0);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //执行命令
        $result = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //返回获得的数据
        return $result;
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 根据两地之间的距离得出经纬度、然后调用计算距离的方法、最后得出两地之间的实际距离
     * @version 1.0.0
     * @funName distance
     * @param $addressa
     * @param $addressb
     * @return  Obj
     */
    public function distance($addressa,$addressb){
        //A地
        $url="http://api.map.baidu.com/geocoder/v2/?address=".$addressa."&output=json&ak=".config::get("baidu_map_key");
        $result=$this->curl_get($url);
        $result=json_decode($result,true);
        $coordinateA=$result['result']['location']['lng'].",".$result['result']['location']['lat'];
        //B地
        $url="http://api.map.baidu.com/geocoder/v2/?address=".$addressb."&output=json&ak=".config::get("baidu_map_key");
        $result=$this->curl_get($url);//http://api.map.baidu.com/geocoder/v2/?address=%E5%8C%97%E4%BA%AC%E5%B8%82%E6%B5%B7%E6%B7%80%E5%8C%BA%E4%B8%8A%E5%9C%B0%E5%8D%81%E8%A1%9710%E5%8F%B7&output=json&ak=7pjv2qlebFh7vSXSO8GPWmfO84CLc2G8
        $result=json_decode($result,true);
        $coordinateB=$result['result']['location']['lng'].",".$result['result']['location']['lat'];
        //经纬度调换
        $number=$this->GetDistance($coordinateA,$coordinateB);
        return $number;
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 计算两地之间的距离
     * @version 1.0.0
     * @funName GetDistance
     * @param $addressa
     * @param $addressb
     * @return  Obj
     */
    public function GetDistance($addressa,$addressb) {
        //拆分地址1
        $explodeA=explode(",",$addressa);
        $lat1=$explodeA[1];
        $lng1=$explodeA[0];
        //拆分地址2
        $explodeA=explode(",",$addressb);
        $lat2=$explodeA[1];
        $lng2=$explodeA[0];
        // 将角度转为狐度
        $radLat1 = deg2rad($lat1); //deg2rad()函数将角度转换为弧度
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = (2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000)/1000;
        return $s;
    }
}