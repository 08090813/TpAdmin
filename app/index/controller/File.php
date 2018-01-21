<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/11
 * Time: 17:21
 */
namespace app\index\controller;
use think\Config;
use think\Controller;
use think\Request;
use app\common\controller\common as common;
class File{
    protected $common;
    public function __construct()
    {
        $this->common = new common();
    }
    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 骑手图片上传
     * @version 1.0.0
     * @funName upfile
     * @return  Obj
     */
    public function upfile(){
        $file = request()->file('image');
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        if($info){
            //拼接完整的文件名返回
            $fileName = Config::get("head_img").$info->getSaveName();
            //替换路径中斜杠
            $fileName = str_replace("\\","/",$fileName);
            $this->common->ajaxSuccess(200,"文件上传成功！",$fileName);
        }else{
            // 上传失败获取错误信息
            $this->common->ajaxError(400,$file->getError());
        }
    }
}