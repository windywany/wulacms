<?php
/**
 * RESTful 访问验证接口，为RestServer提供APPKEY和检查访问权限。
 * @author ngf
 *
 */
interface IRestAccessCheck {
	/**
	 * 根据$appkey取其相应的app secret.
	 *
	 * @param string $appkey
	 *        	app key.
	 * @return string app secret.
	 */
	function getAppSecret($appkey);	
}
?>