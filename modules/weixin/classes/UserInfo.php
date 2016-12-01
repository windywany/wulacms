<?php

namespace weixin\classes;

class UserInfo implements \ArrayAccess {
	private $weixin;
	private $openid;
	private $attr;
	public function __construct($openid, $weixin) {
		if (empty ( $openid )) {
			trigger_error ( 'openid is null' );
		}
		if (empty ( $openid )) {
			trigger_error ( 'weixin account is null' );
		}
		$this->weixin = $weixin;
		$this->openid = $openid;
	}
	/**
	 * 从微信加载用户信息.
	 *
	 * @return boolean 加载成功返回true,反之返回false.
	 */
	public function loadFromWeixin() {
		$openid = $this->openid;
		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=#TOKEN#&openid={$openid}&lang=zh_CN";
		$info = \WeixinUtil::apiGet ( $url, 1, 'weixin_response_filter' );
		if (is_array ( $info ) && isset ( $info ['openid'] )) {
			$this->attr = $info;
			$this->attr ['weixinid'] = $this->weixin;
			$this->attr ['create_time'] = $this->attr ['update_time'] = time ();
			dbsave ( $this->attr, array ('weixinid' => $this->weixin,'openid' => $this->attr ['openid'] ) )->into ( '{weixin_subscriber}' )->exec ();
			return true;
		} else {
			$this->attr = array ();
			log_debug ( '[WEI XIN] Cannot get userinfo' );
			return false;
		}
	}
	/**
	 * 从数据库加载用户信息.如果未加载到则从微信加载.
	 *
	 * @return boolean 加载成功返回true,反之返回false.
	 */
	public function loadFromDB() {
		$info = dbselect ( '*' )->from ( '{weixin_subscriber}' )->where ( array ('weixinid' => $this->weixin,'openid' => $this->openid ) )->get ( 0 );
		if ($info) {
			$this->attr = $info;
			return true;
		} else {
			return $this->loadFromWeixin ();
		}
	}
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see ArrayAccess::offsetExists()
	 */
	public function offsetExists($offset) {
		return isset ( $this->attr [$offset] );
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see ArrayAccess::offsetGet()
	 */
	public function offsetGet($offset) {
		return $this->attr [$offset];
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see ArrayAccess::offsetSet()
	 */
	public function offsetSet($offset, $value) {
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see ArrayAccess::offsetUnset()
	 */
	public function offsetUnset($offset) {
	}
}