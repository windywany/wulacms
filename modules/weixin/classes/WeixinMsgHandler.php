<?php

namespace weixin\classes;

class WeixinMsgHandler {

	public static function msgReply($type = '', $xml) {
		//微信配置信息
		$AppID          = cfg('AppID@weixin');
		$AppSecret      = cfg('AppSecret@weixin');
		$Token          = cfg('Token@weixin');
		$EncodingAESKey = cfg('EncodingAESKey@weixin');
		switch ($type) {
			case 'sub':
				$msg = \WeixinMsgSubForm::getContent();
				break;
			default:
				$msg = \WeixinMsgKeywordForm::getKeywordContent($xml->Content);
				break;
		}
		//初始化该值
		$chatWX = new Wechat(array('token' => $Token, 'encodingaeskey' => $EncodingAESKey, 'appid' => $AppID, 'appsecret' => $AppSecret));
		$token  = \WeixinUtil::getAccessToken();
		$chatWX->checkAuth(null, null, $token);

		//获取基本信息
		$chatWX->getRev();

		if ($msg['MsgType'] == 'text') {
			$text = $chatWX->text($msg['Content'])->reply();
		} else if ($msg['MsgType'] == 'image') {
			$msg  = \WeixinMsgKeywordForm::getKeywordContent($xml->Content);
			$text = $chatWX->image($msg['MediaId'])->reply();
		} else if ($msg['MsgType'] == 'voice') {
			$text = $chatWX->voice($msg['MediaId'])->reply();
		} else if ($msg['MsgType'] == 'video') {
			$text = $chatWX->video($msg['MediaId'], $msg['Title'], $msg['Description'])->reply();
		} else if ($msg['MsgType'] == 'music') {
			$text = $chatWX->music($msg['Title'], $msg['Description'], $msg['MusicURL'], $msg['HQMusicUrl'], $msg['ThumbMediaId'])->reply();
		} else if ($msg['MsgType'] == 'news') {
			$text = $chatWX->news($msg['Articles'])->reply();
		}
	}

	public static function on_weixin_message_text($rtn, $xml, $login) {
		self::msgReply('text', $xml);
	}
}

?>