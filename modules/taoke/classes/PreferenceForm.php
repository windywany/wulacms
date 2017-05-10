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

	private $appkey     = array('group' => '2', 'col' => '4', 'label' => 'appkey', 'default' => '',);
	private $appsecret  = array('group' => '2', 'col' => '4', 'label' => 'appsecret', 'default' => '');
	private $user_id    = array('group' => '2', 'col' => '2', 'label' => '淘宝用户ID', 'note' => '淘宝用户ID，请到源码中查找');
	private $workerNum  = array('group' => '2', 'col' => '2', 'label' => '导入进程数', 'default' => 2, 'rules' => ['regexp(/^[1-9]\d?/)' => '只能是数字']);
	private $share_url  = array('group' => '3', 'col' => 4, 'label' => '分享url', 'note' => '分享url');
	private $search_url = array('group' => '3', 'col' => 4, 'label' => '搜索URL', 'note' => '分享url');
	private $hot_search = array('group' => '3', 'col' => 4, 'label' => '热门搜索', 'note' => '请以逗号隔开。如：A,B,C,D');
	private $tpl        = array('label' => '淘口令弹框内容', 'default' => '{title}... 原价{price}元，抢券立省{discount}元', 'note' => '{platform}:平台;{title}:商品标题;{price}:原价;{real_price}:券后价格;{conpou_price}:优惠券价格;{discount}:折扣;{coupon_remain}:剩余;{coupon_stop}:结束日期;{wangwang}:旺旺;{shopname}:店铺');
	private $replyTpl   = array('label' => '回复模板', 'widget' => 'textarea', 'default' => '亲，一共为你找到{total}件与『{goods}』有关的商品有优惠券可用哦，【<a href="http://www.abc.com/ss/{key}">点我将优惠券拿走吧</a>】手慢无哦^_^', 'note' => '自动回复用户的消息,{goods}用户输入的内容,{result}搜索结果,{total}找到多少件.');
	private $replyTpl1  = array('label' => '未找到回复模板', 'default' => '亲，奴家找不到与『{goods}』相关的商品哇，试试其它的好吗(」ﾟヘﾟ)」', 'note' => '自动回复用户的消息,{goods}用户输入的内容.');
	private $word       = array('widget' => 'textarea', 'row' => '6', 'label' => '推广语', 'default' => '', 'note' => '{platform}:平台;{title}:商品标题;{price}:原价;{real_price}:券后价格;{url}:链接;{token}:淘口令;{conpou_price}:优惠券价格;{discount}:折扣;{coupon_remain}:剩余;{coupon_stop}:结束日期;{wangwang}:旺旺;{shopname}:店铺;{reason}:推荐理由');
	private $fuzzy      = array('label' => '模糊搜索', 'widget' => 'radio', 'default' => '1', 'defaults' => "1=启用\n0=禁用");
}