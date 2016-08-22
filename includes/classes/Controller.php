<?php
/**
 * Controller.
 *
 * @author guangfeng.ning
 *
 */
abstract class Controller {
	protected $request;
	protected $response;
	protected $user;
	protected $acls = false;
	protected $checkUser = false;
	/**
	 * construct.
	 *
	 * @param Request $req
	 * @param Response $res
	 * @param string $method
	 * @param string $http_method
	 */
	public function __construct($req, $res) {
		$this->request = $req;
		$this->response = $res;
	}
	/**
	 * 检测用户登录,如果未登录将跳转到登录页面.
	 *
	 * @param string $url
	 *        	未登录时跳转的地址.
	 * @param string $type
	 *        	用户类型.
	 */
	protected function checkLogin($url = 'dashboard') {
		if (! $this->user->isLogin ()) {
			$this->user = apply_filter ( 'on_unauth_access', $this->user );
			if (! $this->user->isLogin ()) {
				if (preg_match ( '#^(ht|f)tps?://.+#', $url )) {
					Response::redirect ( $url );
				} else {
					Response::redirect ( tourl ( $url ) );
				}
			}
		}
	}
	
	/**
	 * 在运行前执行的方法.
	 *
	 * @param string $method
	 *        	要执行的方法.
	 */
	public function preRun($method) {
		if ($this->checkUser) {
			// 检验用户登录和用户类型.
			if (is_array ( $this->checkUser )) {
				$len = count ( $this->checkUser );
				if ($len == 1) {
					$this->user = whoami ( 'admin' );
					$this->checkLogin ( $this->checkUser [0] );
				} else {
					$url = array_shift ( $this->checkUser );
					$type = array_shift ( $this->checkUser );
					$this->user = whoami ( empty ( $type ) ? 'admin' : $type );
					if (empty ( $this->checkUser ) || ! in_array ( $method, $this->checkUser )) {
						$this->checkLogin ( $url );
					}
				}
			} else {
				$this->user = whoami ( 'admin' );
				$this->checkLogin ();
			}
		} else if ($this->checkUser === false) {
			$this->user = whoami ( 'admin' );
		}
		$this->_check_acls ( $method );
	}
	/**
	 * 在运行之后执行的方法。
	 * 注:当用户在主方法体或preRun中退出(exit,die,Response->close,Response::redirect)，此方法可能不被调用。
	 *
	 * @param View $view
	 *        	主方法返回的视图.
	 * @return SmartyView 如果替换$view可以直接返回一个SmartyView实例.
	 */
	public function postRun(&$view) {
		return null;
	}
	/**
	 * 权限检测.
	 *
	 * @param unknown $method
	 */
	protected function _check_acls($method) {
		if (is_array ( $this->acls ) && ! empty ( $this->acls )) {
			$acls = $this->acls;
			// 找到对应方法的ACL规则
			$acl = isset ( $this->acls [$method] ) ? $this->acls [$method] : (isset ( $this->acls ['*'] ) ? $this->acls ['*'] : false);
			if ($acl) {
				$acl = explode ( ';', $acl ); // 分隔多个规则
				$rst = false;
				// 处理每一个规则
				foreach ( $acl as $a ) {
					$as = explode ( '|', trim ( $a ) );
					$len = count ( $as );
					// 只有规则，没有字段控制.
					if ($len == 1) {
						$rst = icando ( $as [0], $this->user );
						break;
					} else {
						// 分隔出字段
						$fields = explode ( ',', $as [0] );
						$find = true;
						// 假设找到
						foreach ( $fields as $f ) {
							$find &= isset ( $this->request [$f] ) && (is_numeric ( $this->request [$f] ) || ! empty ( $this->request [$f] ));
							if (! $find) {
								break;
							}
						}
						// 如果每个字段都找到，则进行权限验证
						if ($find) {
							$rst = icando ( $as [1], $this->user );
							break;
						}
					}
				}
				// 权限验证失败
				if (! $rst) {
					$rtn = apply_filter ( 'on_check_acl_failed', false, $this->user );
					if ($rtn === false) {
						Response::showErrorMsg ( '你无权进行此操作，请与管理员联系.', 403 );
					}
				}
			}
		}
	}
}