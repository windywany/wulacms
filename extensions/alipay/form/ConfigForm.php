<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace alipay\form;

class ConfigForm extends \AbstractForm {
	private $partner     = ['label' => '合作身份者ID，签约账号', 'note' => '以2088开头由16位纯数字组成的字符串', 'rules' => ['required' => '请填写签约账号', 'regexp(/^2088\d{12}$/)' => '请正确填写以2088开头由16位纯数字组成的字符串']];
	private $private_key = ['label' => '商户的私钥', 'widget' => 'textarea', 'row' => 10, 'rules' => ['required' => '请填写商户的私钥'], 'note' => 'RSA公私钥生成：https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.nBDxfy&treeId=58&articleId=103242&docType=1'];

	private $alipay_public_key = ['label' => '支付宝的公钥', 'widget' => 'textarea', 'row' => 4, 'rules' => ['required' => '请填写支付宝的公钥'], 'note' => '查看地址:https://b.alipay.com/order/pidAndKey.htm'];

	private $domain = ['label' => '回调域名', 'rules' => ['required' => '请正确填写回调域名']];

	private $transport = ['label' => '访问方式', 'widget' => 'radio', 'default' => 'http', 'defaults' => "http=HTTP\nhttps=HTTPS"];

	private $anti_phishing = ['label' => '防钓鱼机制', 'widget' => 'radio', 'default' => '0', 'defaults' => "0=禁用\n1=启用"];

	protected function init_form_fields($data, $value_set) {
		$host = REAL_HTTP_HOST;
		$this->getField('domain')->setOptions(['default' => REAL_HTTP_HOST]);
	}

}