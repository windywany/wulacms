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
	protected $acls = [];
	protected $checkUser = false;
	/**
	 * @var \Notoj\ReflectionClass
	 */
	 public $reflection = null;
	/**
	 * construct.
	 *
	 * @param Request $req
	 * @param Response $res
	 */
	public function __construct($req, $res) {
		$this->request = $req;
		$this->response = $res;
		if(ANNOTATION_SUPPORT){
			$this->reflection= new Notoj\ReflectionClass($this);
			$this->annotations= $this->reflection->getAnnotations();
			if($this->annotations->has('checkUser')){
				$checkUser = $this->annotations->get('checkUser');
				$args = $checkUser[0]->getArgs();
				if(count($args) > 0) {
					$args = $args[0];
				}else{
					$args = null;
				}
				if($args){
					$this->checkUser = ['dashboard','admin'];
					if(isset($args['url'])){
						$this->checkUser[0] = $args['url'];
					}
					if(isset($args['type'])){
						$this->checkUser[1] = $args['type'];
					}
				}else{
					$this->checkUser = true;
				}
			}
		}
	}
	/**
	 * 检测用户登录,如果未登录将跳转到登录页面.
	 *
	 * @param string $url
	 *        	未登录时跳转的地址.
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
		if($this->reflection ){
			$md = $this->reflection->getMethod($method);
			$anno = $md->getAnnotations();
			$checkUser = $anno->get('checkUser');
			if($checkUser && $checkUser[0]){
				$checkUser = $checkUser[0]->getArg(0);
				if($checkUser == 'false'|| $checkUser == '0'){
					return;
				}
			}
		}else{
			$anno = null;
		}
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
		$this->_check_acls ( $method,$anno );
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
		return $view;
	}
	/**
	 * 权限检测.
	 *
	 * @param string $method
	 * @param \Notoj\Annotation\Annotation $anno
	 */
	protected function _check_acls($method,$anno=null) {
		if($anno && !isset ( $this->acls [$method] )){
			if($anno->has('acl')){
				$acl = $anno->get('acl');
				if($acl[0]){
					$acl = $acl[0]->getArg(0);
					if($acl){
						$this->acls[$method] = $acl;
					}
				}
			}
		}
		if (is_array ( $this->acls ) && ! empty ( $this->acls )) {
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