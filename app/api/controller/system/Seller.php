<?php
/**
 * @Author 张超.
 * @Copyright http://www.zhangchao.name
 * @Email 416716328@qq.com
 * @DateTime 2018/3/6 17:13
 * @Desc
 */
namespace app\api\controller\system;
use app\api\controller\Base;
use app\api\model\SellerAuthInfo;
use app\api\model\Seller as SellerModel;
use think\Controller;
use app\common\controller\common;
use think\Request;
use think\Db;
class Seller extends Base
{
    protected $sign;
    protected $header;
    protected $sellerId;
    protected $param;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->param = $request;
        //签名验证
        if (!$request->post("sign")){
            $this->common->ajaxError(401,"缺少sign签名");
        }
        //验证sign
        $this->sign=$this->verifySign($request->param(),$request->post("sign"));
        if ($this->sign!==true){
            $this->common->ajaxError(401,"sign签名错误",$this->sign);
        }
        //token验证
        $this->header = Request::instance()->header();
        $this->common = new common();
        //验证token是否正确
        if (!$request->post("sign")){
            $this->common->ajaxError(403,"参数缺少或错误！");
        }
        //验证sign
        $this->sign=parent::verifySign($request->param(),$request->post("sign"));
        if ($this->sign!==true){
            $this->common->ajaxError(401,"sign签名错误！",$this->sign);
        }
        if (!$this->header["token"]){
            $this->common->ajaxError(403,"参数缺少或错误！");
        }
        $this->sellerId = $request->post("sellerid");
        $token = parent::verifyToken($this->header["token"],$this->sellerId);
        if ($token!==false){
            $token = (array)$token;
            //判断token时间是否已过期
            if ($token['exp']>time()){

            }else{
                $this->common->ajaxError(402,"token已过期");//token后期存入数据库之后、刷新token的过期时间
            }
        }else{
            $this->common->ajaxError(403,"TOKEN错误！");
        }
    }

    /**
     * @author by 张超 <Email:416716328@qq.com web:http://www.zhangchao.name>
     * @name 商家提交实名认证资料审核
     * @version 1.0.0
     * @funName pushData
     * @return  Obj
     */
    public function pushData(){
        $data['name'] = $this->param->post("name");//真实姓名
        $data['card_no'] = $this->param->post("card_no");//身份证号
        $data['company_name'] = $this->param->post("company_name");//企业全称
        $data['company_code'] = $this->param->post("company_code");//企业统一信用代码
        $data['company_legal'] = $this->param->post("company_legal");//法人真实姓名
        $data['phone'] = $this->param->post("phone");//实名认证的手机号码
        $data['id_img'] = $this->param->post("id_img");//身份证正面
        $data['license_img'] = $this->param->post("license_img");//营业执照正本
        $data['seller_id'] = $this->sellerId;//商家id
        $data['status'] = 1;
        Db::startTrans();
        try{
            $sellerInfo = new SellerAuthInfo();
            $infoStatus = $sellerInfo->allowField(true)->insert($data);//插入商家审核信息表
            $seller = new SellerModel();
            $auth_status = $seller->find($this->sellerId);
            if (in_array($auth_status['auth_status'],array(2,3))){
                $this->common->ajaxError(400,"请勿重复提交资料！");
            }
            $sellerStatus = $seller->save(array('auth_status'=>2),array('id'=>$this->sellerId));
            if ($infoStatus&&$sellerStatus){
                Db::commit();
                $this->common->ajaxSuccess(200,"资料提交成功、请耐心等待审核！");
            }else{
                Db::rollback();
                $this->common->ajaxError(400,"提交失败、请稍后再试！");
            }
        }catch (\Exception $e){
            $this->common->ajaxError(400,"提交失败、请稍后再试！");
        }
    }
    public function testfile(){
        $this->common->AppFile();
    }
}