<?php

namespace weixin\classes;

class WeixinEventHandler {
	
	/**
	 *
	 * @param SimpleXMLElement $xml        	
	 */
	public static function on_weixin_event_subscribe($rtn, $xml, $type) {
		if (! $type) {
			$openid = $xml->FromUserName;
			$user = new UserInfo ( $openid, $xml->ToUserName );
			$user->loadFromWeixin ();
		}
		//订阅欢迎消息
		WeixinMsgHandler::msgReply('sub',$xml);
		return $rtn;
	}
	public static function on_weixin_event_scan($rtn, $xml, $type) {
		log_debug ( '[weixin] SCAN:' . $xml->FromUserName );
		return $rtn;
	}
	/**
	 *
	 * @param SimpleXMLElement $xml        	
	 */
	public static function on_weixin_event_unsubscribe($rtn, $xml, $type) {
		if (! $type) {
			$openid = $xml->FromUserName;
			$where ['weixinid'] = $xml->ToUserName;
			$where ['openid'] = $openid;
			$data ['update_time'] = time ();
			$data ['subscribe'] = 0;
			dbupdate ( '{weixin_subscriber}' )->set ( $data )->where ( $where )->exec ();
		}
		return $rtn;
	}
}

?>