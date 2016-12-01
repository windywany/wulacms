<?php
 /**
    * 微信企业红包
    * @author FLY
    * @date 2016年3月18日 18:05 PM
    * 
    */
class WeixinBonusForm extends AbstractForm {
	private $MchId       = array ('label' => '商户号(微信支付分配的商户号)','group' => 1,'col' => 4,'rules' => array ('digits' => '商户号只能是数字.','regexp(/^[1-9][\d]*$/)' => '商户号只能是数字') );
    private $ServiceIP   = array ('label' => '服务器IP(当前域名指向的公网地址)','group' => 1,'col' => 4,'rules' => array ('required'=>'请填写服务器IP') );
    private $PaySecret   = array ('label' => '支付密钥()','group' => 1,'col' => 4,'rules' => array ('required'=>'请填写支付安全密钥') );
    private $Pempath     = array ('label' => '证书路径(服务器绝对路径)','group' => 1,'col' => 4,'rules' => array ('required' => '请填写证书路径' ) );
    private $Actname     = array ('label' => '红包名称','group' => 1,'col' => 4,'rules' => array ('required' => '请填写红包名称' ) );
    private $Wishing     = array ('label' => '红包祝福语','group' => 1,'col' => 4,'rules' => array ('required' => '请填写红包祝福语' ) );
    private $Remark      = array ('label' => '红包备注','group' => 1,'col' => 4,'rules' => array ('required' => '请填写备注' ) );
}

