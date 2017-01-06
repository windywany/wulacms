<?php

/**
 * 微信回调接口.
 * @author leo
 *
 */
class WeixinController extends NonSessionController {
	/**
	 * 接入认证接口.
	 *
	 * @param string $signature
	 *            签名.
	 * @param int    $timestamp
	 *            unix timestamp.
	 * @param string $nonce
	 *            随机数.
	 * @param string $echostr
	 *            校验成功后返回给微信的字符串.
	 *
	 * @return mixed
	 */
	public function index($signature, $timestamp, $nonce, $echostr) {
		$args [] = $nonce;
		$args [] = $timestamp;
		if (rqset('login')) {
			$args [] = cfg('LoginToken@weixin', 'qbtest');
		} else {
			$args [] = cfg('Token@weixin', 'qbtest');
		}
		if (WeixinUtil::checkSignature($args, $signature)) {
			return $echostr;
		} else {
			log_warn('[weixin] 微信接入签名校验失败', 'weixin');
			Response::respond(403);
		}
	}

	/**
	 * 接收消息 (普通消息,事件推送).
	 *
	 */
	public function index_post() {
		$content = @file_get_contents('php://input');
		if ($content) {
			$isLoginAccount = rqset('login');
			$args []        = rqst('nonce');
			$args []        = rqst('timestamp');
			if ($isLoginAccount) {
				$args [] = cfg('LoginToken@weixin', 'qbtest');
			} else {
				$args [] = cfg('Token@weixin', 'qbtest');
			}

			if (WeixinUtil::checkSignature($args, rqst('signature'))) {
				$xml = simplexml_load_string($content);
				if ($xml) {
					$msgType = strtolower($xml->MsgType);
					if ($msgType == 'event') {
						$rtn = apply_filter('on_weixin_event_' . strtolower($xml->Event), 'success', $xml, $isLoginAccount);
					} else {
						$rtn = apply_filter('on_weixin_message_' . $msgType, 'success', $xml, $isLoginAccount);
					}

					if ($rtn !== false) {
						return $rtn;
					}

					return 'success';
				} else {
					log_debug('[weixin] 无法解析数据:' . $content, 'weixin');
				}
			} else {
				log_warn('[weixin] 微信接收消息签名校验失败', 'weixin');
			}
		} else {
			log_warn('[weixin] 未读取到数据', 'weixin');
		}
		Response::respond(403);
	}
}