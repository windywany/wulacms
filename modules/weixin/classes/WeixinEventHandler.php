<?php

namespace weixin\classes;

class WeixinEventHandler {

	/**
	 *
	 * @param string            $rtn
	 * @param \SimpleXMLElement $xml
	 * @param bool              $type
	 *
	 * @return string
	 */
	public static function on_weixin_event_subscribe($rtn, $xml, $type) {
		if (!$type) {
			$openid = $xml->FromUserName;
			$user   = new UserInfo ($openid, $xml->ToUserName);
			$user->loadFromWeixin();
		}

		//订阅欢迎消息
		return WeixinMsgHandler::msgReply('sub', $xml);
	}
}