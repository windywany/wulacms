<?php
namespace weixin\classes;
 /**
    * 微信企业红包
    * 企业向用户直接发送定额红包
    * 官方接口
    * https://pay.weixin.qq.com/wiki/doc/api/tools/cash_coupon.php?chapter=13_5
    * 官方证书
    * https://pay.weixin.qq.com/wiki/doc/api/tools/cash_coupon.php?chapter=4_3
    * @author FLY
    * @date 2016年3月19日
    */

class WeixinBonus{

     const RE_URL           = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
     const TOTAL_NUM        = 1;

     private  $sign         = '';
     private  $mch_id       = '';
     private  $wxappid      = '';
     private  $send_name    = '';
     private  $total_amount = '';
     private  $wishing      = '';
     private  $act_name     = '';
     private  $remark       = '';
     private  $pem_path     = '';
     private  $client_ip    = '';
     private  $re_openid    = '';
    /*
     * 从后台系统配置中读取
     * 相关的数据
     * */
   function __construct()
    {
        $this->mch_id       = cfg('MchId@weixin');
        $this->send_name    = cfg('Actname@weixin');
        $this->wishing      = cfg('Wishing@weixin');
        $this->act_name     = cfg('Actname@weixin');
        $this->remark       = cfg('Remark@weixin');
        $this->pem_path     = cfg('Pempath@weixin');
        $this->client_ip    = cfg('ServiceIP@weixin');
        $this->wxappid      = cfg('AppID@weixin');
    }
    /*
     * @param $open id  接受用户在公众号下的id
     * @param $money_sum 金额 以分为单位
     *
     * */
    public function  send_bonus($open_id,$money_sum)
    {
        /*验证后*/
        $this->re_openid    = $open_id;
        $this->total_amount = $money_sum;
        /*请求发送*/
        $vars = $this->arr2xml($this->param_prepare());
        $result = $this->curl_post_ssl(self::RE_URL,$vars);/*xml*/

        if (!is_array($result)&& $result)
        {
            $arr = (array)simplexml_load_string($result,'SimpleXMLElement', LIBXML_NOCDATA);

            /*
             * Array
                (
                    [return_code] => SUCCESS
                    [return_msg] => 发放成功.
                    [result_code] => SUCCESS
                    [err_code] => 0
                    [err_code_des] => 发放成功.
                    [mch_billno] => 0010010404201411170000046545
                    [mch_id] => 10010404
                    [wxappid] => wx6fa7e3bab7e15415
                    [re_openid] => onqOjjmM1tad-3ROpncN-yUfa6uI
                    [total_amount] => 1
                    [send_listid] => 100000000020150520314766074200
                    [send_time] => 20150520102602
                )
             * */
            return $arr;
        }
        return false;
    }

    /*拼装所需请求字段*/
    private function param_prepare()
    {
        $params                 = array();
        $params['nonce_str']    = rand_str(32,'a-z,0-9,A-Z');/*随机字符串*/
        $params['mch_billno']   = $this->get_bill_no($this->mch_id);/*商户订单号*/
        $params['mch_id']       = $this->mch_id;/*商户号*/
        $params['wxappid']      = $this->wxappid;/*公众账号appid*/
        $params['send_name']    = $this->send_name;/*商户名称*/
        $params['re_openid']    = $this->re_openid;/*用户openid*/
        $params['total_amount'] = $this->total_amount;/*付款金额*/
        $params['total_num']    = self::TOTAL_NUM;/*红包发放总人数*/
        $params['wishing']      = $this->wishing;/*红包祝福语*/
        $params['client_ip']    = $this->client_ip;/*Ip地址*/
        $params['act_name']     = $this->act_name;/*活动名称*/
        $params['remark']       = $this->remark;
        $this->sign             = $this->get_sign($params);
        $params['sign']         = $this->sign;/*签名*/

        return $params;
    }

    /* mch_billno 商户订单号（每个订单号必须唯一）组成：mch_id+yyyymmdd+10位一天内不能重复的数字。*/
    private  function get_bill_no($pre)
    {
        $no = date('YmdHis').substr(microtime(),2,4);
        return $pre.$no;
    }

    /*
     * 生成签名
     * https://pay.weixin.qq.com/wiki/doc/api/tools/cash_coupon.php?chapter=4_3
     * @param array
     * @return string
     * */
    private function get_sign($params,$urlencode=false)
    {
        if(!is_array($params)|| count($params)<1)
        {
            return false;
        }

        ksort($params);

        $aPOST = array();
        foreach($params as $key=>$val)
        {
            $aPOST[] = $key."=".urlencode($val);
        }

        $str =  join("&", $aPOST).'&key='.cfg('PaySecret@weixin');
        return strtoupper(MD5($str));
    }
    /**
     * POST 请求
     * @param string $url
     * @param string  $vars
     * @param int $second 30
     * @return xml $sContent
     */
    private function curl_post_ssl($url,$vars,$second=30)
    {
        $oCurl  = curl_init();
        curl_setopt($oCurl,CURLOPT_TIMEOUT,$second);
        curl_setopt($oCurl,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_URL, $url);
        /*https必须加这个参数*/
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
//        curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1

        /*证书*/
//        curl_setopt($oCurl,CURLOPT_SSLCERT,$this->pem_path.'/cert.pem');
//        curl_setopt($oCurl,CURLOPT_SSLKEY,$this->pem_path.'/private.pem');
//        curl_setopt($oCurl,CURLOPT_CAINFO,$this->pem_path.'/rootca.pem');
        /*2证合一*/
        curl_setopt($oCurl,CURLOPT_SSLCERT,$this->pem_path.'/all.pem');

        curl_setopt($oCurl, CURLOPT_POST,1);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS,$vars);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);


        if($sContent){
            curl_close($oCurl);
            return $sContent;
        }
        else {
            //$error = curl_errno($oCurl);
           // echo "call faild, errorCode:$error\n";
            curl_close($oCurl);
            return false;
        }
    }

    /*数组转成xml*/
    private function arr2xml($arr)
    {
        if(!is_array($arr)|| count($arr)<1)
        {
            return false;
        }

        $xml   = "<xml>";

        foreach($arr as $key=>$val)
        {
                $xml .="<".$key."><![CDATA[".$val."]]></".$key.">";
        }

        $xml .="</xml>";
        return $xml;
    }

}

