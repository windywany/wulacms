<?php
class WeixinUtil {
	
	/**
	 * 校验参数是否正确.具体校验方法见{@link http://mp.weixin.qq.com/wiki/17/2d4265491f12608cd170a95559800f2d.html}.
	 *
	 * @param array $param
	 *        	要校验的参数数组.
	 * @param string $signature
	 *        	微信传过来的签名.
	 * @return bool 如果签名一致返回true,反之返回false.
	 */
	public static function checkSignature($param, $signature) {
		sort ( $param, SORT_STRING );
		$tmpStr = implode ( $param );
		$tmpStr = sha1 ( $tmpStr );
		if ($tmpStr == $signature) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 取微信的access_token。
	 *
	 * @return mixed access_token or null for failure.
	 */
	public static function getAccessToken($force = false) {
		if (! $force) {
			$access_token = cfg ( 'access_token@weixin', null );
			$expire = icfg ( 'token_expire@weixin', 0 );
		} else {
			$access_token = false;
			$expire = 0;
		}
		// 使用缓存中的access_token.
		if ($access_token && $expire > time ()) {
			return $access_token;
		}
		$AppID = cfg ( 'AppID@weixin' );
		$AppSecret = cfg ( 'AppSecret@weixin' );
		if (! $AppID || ! $AppSecret) {
			log_warn ( '[weixin] AppID or AppSecret is null.' );
			return null;
		}
		log_debug ( '[weixin] get access from weixin' );
		// 从服务器获取access_token.
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$AppID}&secret={$AppSecret}";
		$rst = WeixinUtil::apiGet ( $url, 0 );
		if (isset ( $rst ['access_token'] )) {
			$access_token = $rst ['access_token'];
			$expire = $rst ['expires_in'];
			set_cfg ( 'access_token', $access_token, 'weixin' );
			set_cfg ( 'token_expire', time () + $expire, 'weixin' );
			return $access_token;
		} else {
			return null;
		}
	}
	
	/**
	 * 调用微信GET接口.
	 *
	 * @param string $url
	 *        	要调用的接口,使用#TOKEN#代替access_token.
	 * @param number $redo
	 *        	如果调用失败（access_token过期或微信繁忙时的重复调用次数).
	 * @return mixed 成功时接口返回数据，调用失败返回null.
	 */
	public static function apiGet($url, $redo = 1, $data_filter = null) {
		$rtn = array ();
		if (stripos ( $url, '#TOKEN#' ) > 0) {
			$access_token = WeixinUtil::getAccessToken ();
			if ($access_token) {
				$url = str_replace ( '#TOKEN#', $access_token, $url );
			} else {
				log_warn ( '[weixin] 无法获取access token' );
				return array ('errmsg' => '无法获取access token.','errcode' => '-3' );
			}
		}
		
		$curlLib = CurlClient::getClient ( 30 );
		$content = $curlLib->get ( $url );
		if ($content === false) {
			log_warn ( '[weixin]' . curl_errno ( $curlLib->getChannel () ) . ' ' . curl_error ( $curlLib->getChannel () ) );
		}
		$curlLib->close ();
		$rtn = WeixinUtil::checkResponse ( $content, array ('apiGet',$url,$redo - 1,$data_filter ), $data_filter );
		return $rtn;
	}
	
	/**
	 *
	 * @param string $url        	
	 * @param array $data
	 *        	要发送到微信的数据(JSON格式).
	 */
	public static function apiPost($url, $data, $redo = 1, $data_filter = null) {
		if (stripos ( $url, '#TOKEN#' ) > 0) {
			$access_token = WeixinUtil::getAccessToken ();
			if ($access_token) {
				$url = str_replace ( '#TOKEN#', $access_token, $url );
			} else {
				log_warn ( '[weixin] 无法获取access token' );
				return array ('errmsg' => '无法获取access token.','errcode' => '-3' );
			}
		}
		if (is_array ( $data )) {
			$data = urldecode ( json_encode ( $data, JSON_UNESCAPED_UNICODE ) );
		}
		$client = CurlClient::getClient ( 30, array ('Content-Type: application/json' ) );
		$content = $client->post ( $url, $data );
		$client->close ();
		$rst = WeixinUtil::checkResponse ( $content, array ('apiPost',$url,$data,$redo - 1,$data_filter ), $data_filter );
		return $rst;
	}
	
	/**
	 * 检验接口返回值.
	 *
	 * @param string $data
	 *        	接口返回值.
	 * @param array $redo
	 *        	重复调用信息.
	 * @return mixed 成功返回接口结果，null错误.
	 */
	private static function checkResponse($content, $redo, $data_filter = null) {
		$need_redo = false;
		if ($content) {
			if (is_callable ( $data_filter )) {
				$content = call_user_func_array ( $data_filter, array ($content ) );
			}
			
			$data = @json_decode ( $content, true, 512, JSON_BIGINT_AS_STRING );
			
			if ($data) {
				if (isset ( $data ['errcode'] ) && $data ['errcode'] != '0') {
					log_debug ( '[weixin:' . $data ['errcode'] . '] ' . $data ['errmsg'] );
					if (($data ['errcode'] == '42001' || $data ['errcode'] == '40001' || $data ['errcode'] == '40014' || $data ['errcode'] == '48001')) {
						if (WeixinUtil::getAccessToken ( $data ['errcode'] == '42001' || $data ['errcode'] == '40014' )) {
							// access_token过期要重试.
							$need_redo = true;
						}
					} else if ($data ['errcode'] == '-1') {
						// 系统忙，要重试.
						$need_redo = true;
					} else {
						return $data;
					}
				} else {
					if (isset ( $data ['openid'] ) && (! isset ( $data ['unionid'] ) || empty ( $data ['unionid'] ))) {
						$data ['unionid'] = $data ['openid'];
					}
					return $data;
				}
			} else {
				log_debug ( '[weixin] cannot parse: ' . $content );
				return array ('errmsg' => $content,'errcode' => '-4' );
			}
		} else {
			$need_redo = true;
		}
		// 取redo数
		$cnt = $redo [count ( $redo ) - 2];
		if ($need_redo && $cnt >= 0) {
			usleep ( 500 );
			$method = array_shift ( $redo );
			log_debug ( "[weixin] redo:\n" . var_export ( $redo, true ) );
			return call_user_func_array ( array ('WeixinUtil',$method ), $redo );
		}
		log_warn ( '[weixin] 无法连接到微信' );
		return array ('errmsg' => '无法连接到微信','errcode' => '-2' );
	}
	
	/**
	 * 生成js分享代码
	 *
	 * @author DQ
	 *         @date 2016年2月15日 下午5:40:06
	 * @param
	 *        	url 当前网页的url参数，#之后不处理
	 *        	
	 *        	link 分享链接地址
	 *        	title 表弟
	 *        	imgUrl 图片地址
	 *        	desc 描述
	 *        	
	 * @return
	 *
	 */
	public static function buildShareJS($param = array()) {
		// 微信配置信息
		$AppID = cfg ( 'AppID@weixin' );
		$AppSecret = cfg ( 'AppSecret@weixin' );
		$Token = cfg ( 'Token@weixin' );
		$EncodingAESKey = cfg ( 'EncodingAESKey@weixin' );
		// 初始化该值
		$chatWX = new weixin\classes\Wechat ( array ('token' => $Token,'encodingaeskey' => $EncodingAESKey,'appid' => $AppID,'appsecret' => $AppSecret ) );
		$token = \WeixinUtil::getAccessToken ();
		if (! $token) {
			return '';
		}
		$chatWX->checkAuth ( $AppID, $AppSecret, $token );
		$time = time ();
		$rand = rand_str ( 10, "a-z,0-9,A-Z" );
		$sign = $chatWX->getJsSign ( $param ['url'], $time, $rand );
		if ($sign == false) {
			return '';
		}
		
		$allowItemRs = explode ( ',', cfg ( 'share_item@weixin', '' ) );
		
		// js配置
		$debug = cfg ( 'share_status@weixin', 0 );
		if ($debug == 0) {
			$status = 'false';
			$debugAlert = '';
		} else {
			$status = 'true';
			$debugAlert = 'alert(JSON.stringify(res));';
		}
		
		$tmp = array ();
		$tmp [] = "'checkJsApi'";
		foreach ( explode ( ',', cfg ( 'share_item@weixin', '' ) ) as $val ) {
			$tmp [] = "'" . $val . "'";
		}
		
		$jsConfig = "wx.config({debug: " . $status . ",appId: '" . $sign ['appId'] . "',timestamp:" . $sign ['timestamp'] . ",nonceStr: '" . $sign ['nonceStr'] . "',signature: '" . $sign ['signature'] . "',jsApiList: [" . implode ( ',', $tmp ) . "]});";
		
		// 错误提示
		$jsError = "wx.error(function(res){" . $debugAlert . "});";
		
		// js检查
		$jsCheck = "wx.checkJsApi({jsApiList: [" . implode ( ',', $tmp ) . "],success: function(res) {" . $debugAlert . "}});";
		
		$shareItemJs = array ();
		if (in_array ( 'onMenuShareTimeline', $allowItemRs )) {
			$shareItemJs [] = "wx.onMenuShareTimeline({title: '" . $param ['title'] . "',link: '" . $param ['link'] . "',imgUrl: '" . $param ['imgUrl'] . "',success: function () {if(window.onWxShareSuccess){onWxShareSuccess('" . $param ['link'] . "',1);}},cancel: function () {if(window.onWxShareCancel){onWxShareCancel('" . $param ['link'] . "',1);}}});";
		}
		if (in_array ( 'onMenuShareAppMessage', $allowItemRs )) {
			$shareItemJs [] = "wx.onMenuShareAppMessage({title: '" . $param ['title'] . "',desc: '" . $param ['desc'] . "',link: '" . $param ['link'] . "',imgUrl: '" . $param ['imgUrl'] . "',type: 'link',dataUrl: '',success: function () {if(window.onWxShareSuccess){onWxShareSuccess('" . $param ['link'] . "',2);}},cancel: function () {if(window.onWxShareCancel){onWxShareCancel('" . $param ['link'] . "',2);}}});";
		}
		if (in_array ( 'onMenuShareQQ', $allowItemRs )) {
			$shareItemJs [] = "wx.onMenuShareQQ({title:'" . $param ['title'] . "',desc:'" . $param ['desc'] . "',link:'" . $param ['link'] . "',imgUrl:'" . $param ['imgUrl'] . "',success:function(){if(window.onWxShareSuccess){onWxShareSuccess('" . $param ['link'] . "',3);}},cancel:function(){if(window.onWxShareCancel){onWxShareCancel('" . $param ['link'] . "',3);}}});";
		}
		if (in_array ( 'onMenuShareWeibo', $allowItemRs )) {
			$shareItemJs [] = "wx.onMenuShareWeibo({title:'" . $param ['title'] . "',desc:'" . $param ['desc'] . "',link:'" . $param ['link'] . "',imgUrl:'" . $param ['imgUrl'] . "',success:function(){if(window.onWxShareSuccess){onWxShareSuccess('" . $param ['link'] . "',4);}},cancel:function(){if(window.onWxShareCancel){onWxShareCancel('" . $param ['link'] . "',4);}}});";
		}
		if (in_array ( 'onMenuShareQZone', $allowItemRs )) {
			$shareItemJs [] = "wx.onMenuShareQZone({title:'" . $param ['title'] . "',desc:'" . $param ['desc'] . "',link:'" . $param ['link'] . "',imgUrl:'" . $param ['imgUrl'] . "',success:function(){if(window.onWxShareSuccess){onWxShareSuccess('" . $param ['link'] . "',5);}},cancel:function(){if(window.onWxShareCancel){onWxShareCancel('" . $param ['link'] . "',5);}}});";
		}
		return $jsConfig . $jsError . "wx.ready(function(){" . implode ( '', $shareItemJs ) . "});";
	}
}
