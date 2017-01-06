<?php

namespace weixin\classes;

class WeixinMsgHandler {

	public static function msgReply($type = '', $xml) {
		//微信配置信息
		$AppID          = cfg('AppID@weixin');
		$AppSecret      = cfg('AppSecret@weixin');
		$Token          = cfg('Token@weixin');
		$EncodingAESKey = cfg('EncodingAESKey@weixin');
		//初始化该值
		$chatWX              = new Wechat(array('token' => $Token, 'encodingaeskey' => $EncodingAESKey, 'appid' => $AppID, 'appsecret' => $AppSecret));
		$chatWX->logcallback = 'log_error';
		$chatWX->debug       = true;
		$token               = \WeixinUtil::getAccessToken();
		$chatWX->checkAuth(null, null, $token);
		//获取基本信息
		$chatWX->getRev();

		switch ($type) {
			case 'sub':
				$msg = \WeixinMsgSubForm::getContent();
				break;
			default:
				$content = $chatWX->getRevContent();
				$msg     = apply_filter('weixin_auto_reply_' . $type . '_message', null, $xml, $chatWX);
				if ($msg === null) {
					$msg = \WeixinMsgKeywordForm::getKeywordContent($content);
				}

				break;
		}
		if ($msg == null) {
			return 'success';
		}
		$text = 'success';
		if ($msg['MsgType'] == 'text') {
			$text = $chatWX->text($msg['Content'])->reply(null, true);
		} else if ($msg['MsgType'] == 'image') {
			$msg  = \WeixinMsgKeywordForm::getKeywordContent($xml->Content);
			$text = $chatWX->image($msg['MediaId'])->reply(null, true);
		} else if ($msg['MsgType'] == 'voice') {
			$text = $chatWX->voice($msg['MediaId'])->reply(null, true);
		} else if ($msg['MsgType'] == 'video') {
			$text = $chatWX->video($msg['MediaId'], $msg['Title'], $msg['Description'])->reply(null, true);
		} else if ($msg['MsgType'] == 'music') {
			$text = $chatWX->music($msg['Title'], $msg['Description'], $msg['MusicURL'], $msg['HQMusicUrl'], $msg['ThumbMediaId'])->reply(null, true);
		} else if ($msg['MsgType'] == 'news') {
			$text = $chatWX->news($msg['Articles'])->reply(null, true);
		}

		return $text;
	}

	public static function on_weixin_message_text($rtn, $xml, $login) {
		return self::msgReply('text', $xml);
	}

	public static function on_weixin_message_voice($rtn, $xml, $login) {
		return self::msgReply('voice', $xml);
	}
}
