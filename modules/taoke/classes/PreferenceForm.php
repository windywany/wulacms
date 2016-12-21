<?php
/**
 * Created by PhpStorm.
 * DEC :
 * User: wangwei
 * Date: 2016/12/2
 * Time: 10:19
 */

namespace taoke\classes;

class PreferenceForm extends \AbstractForm {
	protected $__cfg_group = 'taoke';

	private $appkey    = array('group' => '2', 'col' => '4', 'label' => 'appkey', 'default' => '',);
	private $appsecret = array('group' => '2', 'col' => '4', 'label' => 'appsecret', 'default' => '');
	private $user_id   = array('group' => '2', 'col' => '4', 'label' => 'user_id','note'=>'淘宝用户ID，请到源码中查找');
	private $tpl       = array('label'=>'淘口令弹框内容','default'=>'{title}... 原价{price}元，抢券立省{discount}元',
	                           'note'=>'{platform}:平台;{title}:商品标题;{price}:原价;{real_price}:券后价格;{conpou_price}:优惠券价格;{discount}:折扣;{coupon_remain}:剩余;{coupon_stop}:结束日期;{wangwang}:旺旺;{shopname}:店铺');
	private $word      = array('widget'=>'textarea','row'=>'6','label' => '推广语', 'default' => '',
	                           'note'=>'{platform}:平台;{title}:商品标题;{price}:原价;{real_price}:券后价格;{url}:链接;{token}:淘口令;{conpou_price}:优惠券价格;{discount}:折扣;{coupon_remain}:剩余;{coupon_stop}:结束日期;{wangwang}:旺旺;{shopname}:店铺');
}