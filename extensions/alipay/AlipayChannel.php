<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace alipay;

use alipay\form\ConfigForm;
use finance\bill\DepositBill;
use finance\models\MemberDepositRecordModel;
use pay\classes\PayChannel;
use pay\classes\PayChannelManager;

class AlipayChannel extends PayChannel {
	public function getName() {
		return '支付宝及时到帐';
	}

	public function getSettingForm($form) {
		return new ConfigForm();
	}

	public function onCallback() {
		$trade_status = $_GET['trade_status'];
		$config       = self::getConfig(false);
		if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS' || $_GET['seller_id'] == $config['seller_id']) {
			$bill          = new DepositBill();
			$bill->id      = $_GET['out_trade_no'];//充值订单编号
			$bill->amount  = $_GET['total_fee'];//充值费用
			$bill->account = $_GET['buyer_email'];//充值账户
			$bill->transid = $_GET['trade_no'];//支付宝交易号
			$deposit       = new MemberDepositRecordModel();
			//调用充值
			if ($deposit->deposit($bill)) {
				$url = PayChannelManager::getSuccessURL($bill->id);
				\Response::redirect($url);
			}
		} else {
			log_error(var_export($_GET, true), 'alipay_error');
		}
		\Response::redirect($url = PayChannelManager::getFailureURL());
	}

	public function onNotify() {
		$config        = self::getConfig(false);
		$alipayNotify  = new \AlipayNotify($config);
		$verify_result = $alipayNotify->verifyNotify();
		if ($verify_result) {//验证成功
			$trade_status = $_POST['trade_status'];
			$config       = self::getConfig(false);
			if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS' || $_POST['seller_id'] == $config['seller_id']) {
				$bill          = new DepositBill();
				$bill->id      = $_POST['out_trade_no'];//充值订单编号
				$bill->amount  = $_POST['total_fee'];//充值费用
				$bill->account = $_POST['buyer_email'];//充值账户
				$bill->transid = $_POST['trade_no'];//支付宝交易号
				$deposit       = new MemberDepositRecordModel();
				//调用充值
				if ($deposit->deposit($bill)) {
					return 'success';
				}
			}
		}
		//验证失败
		log_error(var_export($_POST, true), 'alipay_notify');

		return "fail";
	}

	// 手工对账输入表单.
	function checkForm() {
		return null;
	}

	// 手工对账
	function doCheck() {
		return true;
	}

	public static function getConfig($anti = true) {
		$alipay_config['partner']           = cfg('partner@gateway_alipay');
		$alipay_config['seller_id']         = $alipay_config['partner'];
		$private_key                        = cfg('private_key@gateway_alipay');
		$private_key                        = str_replace(["\r", "\n"], '', trim(preg_replace('/\-{5}[^\-]+\-{5}/', '', $private_key)));
		$alipay_config['private_key']       = $private_key;
		$alipay_config['alipay_public_key'] = cfg('alipay_public_key@gateway_alipay');

		$alipay_config['sign_type']     = strtoupper('RSA');
		$alipay_config['input_charset'] = 'utf-8';
		$alipay_config['cacert']        = __DIR__ . '/cacert.pem';
		$alipay_config['transport']     = cfg('transport@gateway_alipay', 'http');
		$alipay_config['payment_type']  = '1';
		$alipay_config['service']       = 'create_direct_pay_by_user';
		$domain                         = $alipay_config['transport'] . '://' . cfg('domain@gateway_alipay', 'localhost');
		$alipay_config['notify_url']    = $domain . tourl('pay/notify/alipay');
		$alipay_config['return_url']    = $domain . tourl('pay/gateway/alipay');

		$anti_phishing = $anti && bcfg('anti_phishing@gateway_alipay', false) && extension_loaded('dom');
		if ($anti_phishing) {
			$sub                                = new \AlipaySubmit($alipay_config);
			$time                               = $sub->query_timestamp();
			$alipay_config['anti_phishing_key'] = $time;
			$alipay_config['exter_invoke_ip']   = \Request::getIp();
		} else {
			$alipay_config['anti_phishing_key'] = '';
			$alipay_config['exter_invoke_ip']   = '';
		}

		return $alipay_config;
	}

	/**
	 * @param array $data
	 *        [
	 *        'id'=>'商户订单号，商户网站订单系统中唯一订单号，必填',
	 *        'subject'=>'订单名称，必填',
	 *        'amount'=>'付款金额，必填',
	 *        'body'=>'商品描述，可空',
	 *        'extra_param'=>'额外参数'
	 *        ]
	 *
	 * @return string 提交表单或URL
	 */
	public function getPayForm($data) {
		$out_trade_no = $data['id'];//商户订单号，商户网站订单系统中唯一订单号，必填
		$subject      = $data['subject'];//订单名称，必填
		$total_fee    = rtrim($data['amount'], '.0');//付款金额，必填
		//商品描述，可空
		$body          = isset($data['body']) ? $data['body'] : '';
		$extra_param   = isset($data['extra_param']) ? $data['extra_param'] : '';
		$alipay_config = self::getConfig();
		//构造要请求的参数数组，无需改动
		$parameter = array("service" => $alipay_config['service'], "partner" => $alipay_config['partner'], "seller_id" => $alipay_config['seller_id'], "payment_type" => $alipay_config['payment_type'], "notify_url" => $alipay_config['notify_url'], "return_url" => $alipay_config['return_url'], "anti_phishing_key" => $alipay_config['anti_phishing_key'], "exter_invoke_ip" => $alipay_config['exter_invoke_ip'], "out_trade_no" => $out_trade_no, "subject" => $subject, "total_fee" => $total_fee, "body" => $body, "_input_charset" => trim(strtolower($alipay_config['input_charset'])), 'extra_common_param' => $extra_param);

		//建立请求
		$alipaySubmit = new \AlipaySubmit($alipay_config);
		$html_text    = $alipaySubmit->buildRequestForm($parameter, 'get', '确认');

		return $html_text;
	}
}