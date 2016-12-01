<?php

namespace weixin\classes;

class WeixinCrontab {
	public static function crontab($time = 60) {
		$status = cfg ( 'sync_status@weixin', 0 );
		
		if ($status != 1) {
			return false;
		}
		set_cfg ( 'sync_status', 2, 'weixin' );
		
		$weixinid = cfg ( 'Username@weixin' );
		if (! $weixinid) {
			set_cfg ( 'sync_status', 0, 'weixin' );
			return false;
		}
		
		$time = time ();
		$uid = 0;
		
		$dialect = \DatabaseDialect::getDialect ();
		$nextOpenid = '';
		for(;;) {
			$url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=#TOKEN#&next_openid=' . $nextOpenid;
			$return = \WeixinUtil::apiGet ( $url );
			if (! isset ( $return ['count'] ) || $return ['count'] == 0) {
				break;
			}
			if ($return ['data'] ['openid']) {
				foreach ( $return ['data'] ['openid'] as $val ) {
					if (dbselect ()->from ( '{weixin_subscriber}' )->setDialect ( $dialect )->where ( array ('openid' => $val,'weixinid' => $weixinid ) )->exist ( 'id' )) {
						continue;
					}
					$data = array ('create_time' => $time,'create_uid' => $uid,'deleted' => 0,'openid' => $val,'unionid' => $val,'weixinid' => $weixinid,'subscribe' => 1 );
					$rtn = dbinsert ( $data )->into ( '{weixin_subscriber}' )->setDialect ( $dialect )->exec ();
				}
			}
			if (empty ( $return ['next_openid'] )) {
				break;
			}
			$nextOpenid = $return ['next_openid'];
		}
		
		// 加载用户详细信息
		$size = 100;
		$urlPost = 'https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=#TOKEN#';
		$i = 0;
		for(;;) {
			$db = dbselect ( 'openid,subscribe' )->setDialect ( $dialect );
			$list = $db->from ( '{weixin_subscriber}' )->desc ( 'id' )->limit ( $i * $size, $size )->toArray ();
			
			if (empty ( $list )) {
				break;
			}
			
			$postData = array ();
			foreach ( $list as $val ) {
				if ($val ['subscribe'] == '1') {
					$postData [] = array ('openid' => $val ['openid'],'lang' => 'zh_CN' );
				}
			}
			$db->close ();
			if (! $postData) {
				$i ++;
				continue;
			}
			$return = \WeixinUtil::apiPost ( $urlPost, array ('user_list' => $postData ), 1, 'weixin_response_filter' );
			
			if (! isset ( $return ['user_info_list'] )) {
				$i ++;
				continue;
			}
			
			foreach ( $return ['user_info_list'] as $key => $val ) {
				$tmpOpenId = $val ['openid'];
				unset ( $val ['openid'] );
				$val ['update_time'] = $time;
				$val ['update_uid'] = 0;
				dbupdate ( '{weixin_subscriber}' )->set ( $val )->where ( array ('weixinid' => $weixinid,'openid' => $tmpOpenId ) )->setDialect ( $dialect )->exec ();
			}
			$i ++;
		}
		
		set_cfg ( 'sync_status', 0, 'weixin' );
	}
}
