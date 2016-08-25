<?php
/**
 * OAuth Client.
 * @author Guangfeng
 *
 */
class NtzOAuthClient {
	/**
	 * 创建一个OAuthClient 实例。
	 * 
	 * @param string $server
	 *        	OAuth Server URL.
	 * @param string $appid
	 *        	App ID.
	 * @param string $appkey
	 *        	App secret.
	 * @param string $callback
	 *        	回调地址.
	 */
	public function __construct($server, $appid, $appkey, $callback) {
	}
	/**
	 * 登录.
	 */
	public function login() {
	}
	/**
	 * 取访问Token.
	 *
	 * @return string access token.
	 */
	public function getAccessToken() {
	}
	/**
	 * 取用户的OpenID。
	 *
	 * @return open id.
	 */
	public function getOpenID() {
	}
	/**
	 * 取用户信息.
	 *
	 * @return array 用户信息.
	 */
	public function getUserInfo() {
	}
}