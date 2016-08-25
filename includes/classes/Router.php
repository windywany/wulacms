<?php

/**
 * framework router.
 * @author guangfeng.ning
 *
 */
class Router {
	private static $INSTANCE;
	public static $APP2URL = array ();
	public static $URL2APP = array ();
	private static $sticked = array ();
	private $current_url;
	private $parsed_url;
	private $dispacthing_url = '';
	private $current_page = 0;
	private $current_page_data = array ();
	private $url_info = array ();
	private $request_args = array ();
	private function __construct() {
		$host = $_SERVER ['HTTP_HOST'];
		$pos = strpos ( $host, '.' );
		if ($pos !== false && $pos > 0) {
			$host = substr ( $host, 0, $pos );
		} else {
			$host = '';
		}
		define ( 'CUR_SUBDOMAIN', $host );
	}
	
	/**
	 * get the system router.
	 *
	 * @return Router the singleton Router object.
	 */
	public static function getRouter() {
		if (! Router::$INSTANCE) {
			Router::$INSTANCE = new Router ();
		}
		return Router::$INSTANCE;
	}
	/**
	 * mapping the url and to app.
	 *
	 * @param string $url        	
	 * @param string $app        	
	 */
	public static function map($url, $app) {
		if ($url {0} == '#') {
			$url = substr ( $url, 1 );
			Router::$sticked [$url] = true;
		}
		Router::$APP2URL [$app] = $url;
		Router::$URL2APP [$url] = $app;
	}
	/**
	 * app name to url.
	 *
	 * @param string $url        	
	 */
	public static function url($url, $base = true, $root = true) {
		static $base_url = false, $dapp = false;
		if (! $base_url) {
			$base_url = rtrim ( cfg ( 'site_url', DETECTED_ABS_URL ), '/' );
			if (! defined ( 'CLEAN_URL' ) || ! CLEAN_URL) {
				$base_url .= '/index.php';
			}
			$dapp = cfg ( 'default_app' );
		}
		if (is_array ( $url )) {
			if (count ( $url ) > 1) {
				$base = intval ( $url [1] ) == 1;
			}
			$url = $url [0];
		}
		$urls = explode ( '/', $url );
		if (count ( $urls ) > 0) {
			$app = array_shift ( $urls );
		} else {
			return $base ? $base_url : '';
		}
		if ($dapp != $app) {
			if (isset ( Router::$APP2URL [$app] )) {
				array_unshift ( $urls, Router::$APP2URL [$app] );
			} else {
				array_unshift ( $urls, $app );
			}
		}
		if ($base) {
			if ($base_url == '/') {
				array_unshift ( $urls, '' );
			} else {
				array_unshift ( $urls, $base_url );
			}
		}
		$url = trailingslashit ( implode ( '/', $urls ) );
		if ($base && $root && preg_match ( '#^https?://.+#i', $url )) {
			$url = preg_replace ( '#^https?://.+?(/.+)#i', '\1', $url );
		}
		return $url;
	}
	/**
	 * app name to url.
	 *
	 * @param string $url        	
	 */
	public static function urlf($url, $base = true, $root = true) {
		static $base_url = false, $dapp = false;
		if (! $base_url) {
			$base_url = rtrim ( cfg ( 'site_url', DETECTED_ABS_URL ), '/' );
			if (! defined ( 'CLEAN_URL' ) || ! CLEAN_URL) {
				$base_url .= '/index.php';
			}
			$dapp = cfg ( 'default_app' );
		}
		if (is_array ( $url )) {
			if (count ( $url ) > 1) {
				$base = intval ( $url [1] ) == 1;
			}
			$url = $url [0];
		}
		$urls = explode ( '/', $url );
		if (count ( $urls ) > 0) {
			$app = array_shift ( $urls );
		} else {
			return $base ? $base_url : '';
		}
		if ($dapp != $app) {
			if (isset ( Router::$APP2URL [$app] )) {
				array_unshift ( $urls, Router::$APP2URL [$app] );
			} else {
				array_unshift ( $urls, $app );
			}
		}
		if ($base) {
			if ($base_url == '/') {
				array_unshift ( $urls, '' );
			} else {
				array_unshift ( $urls, $base_url );
			}
		}
		$url = trailingslashit ( implode ( '/', $urls ) );
		if ($base && $root && preg_match ( '#^https?://.+#i', $url )) {
			$url = preg_replace ( '#^https?://.+?(/.+)#i', '\1', $url );
		}
		return $url;
	}
	public function getCurrentURL() {
		return $this->current_url;
	}
	public function setCurrentURL($url) {
		$this->current_url = $url;
	}
	public function getParsedString() {
		return $this->parsed_url;
	}
	public function getCurrentPageNo() {
		return $this->current_page;
	}
	/**
	 * 取当前页面数据.
	 *
	 * @return multitype:
	 */
	public function getCurrentPage() {
		$mobi_domain = cfg ( 'mobi_domain' );
		if ($mobi_domain && $mobi_domain == REAL_HTTP_HOST) {
			$this->current_page_data ['bind'] = $_SERVER ['HTTP_HOST'];
			$this->current_page_data ['inherit'] = 2;
		}
		return $this->current_page_data;
	}
	public function getUrlInfo($url = null) {
		if ($url == null) {
			return $this->url_info;
		} else {
			return $this->parseURL ( $url, true );
		}
	}
	/**
	 * 分发页面请求.
	 *
	 * 直接输出页面HTML.
	 */
	public function route($do = '', $dispath = true) {
		global $__kissgo_apps;
		if (empty ( $do )) {
			if (defined ( 'REQUEST_URL' )) {
				$do = trim ( REQUEST_URL, '/' );
			} else if (isset ( $_SERVER ['PATH_INFO'] )) {
				$do = trim ( $_SERVER ['PATH_INFO'], '/' );
			}
		}
		if (empty ( $do ) || preg_match ( '#.+\.(s?html?|xml|jsp|json)$#', $do )) {
			return $this->dispatchPage ( $do, $dispath );
		}
		
		$controllers = explode ( '/', $do );
		$pms = array ();
		$len = count ( $controllers );
		if ($len == 1 && ! empty ( $controllers [0] )) {
			$module = $controllers [0];
			$action = 'index';
		} else if ($len == 2) {
			$module = $controllers [0];
			$action = $controllers [1];
		} else if ($len > 2) {
			$module = $controllers [0];
			$action = $controllers [1];
			$pms = array_slice ( $controllers, 2 );
		} else {
			return $this->dispatchPage ( $do, $dispath );
		}
		$module = strtolower ( $module );
		if (isset ( Router::$URL2APP [$module] )) {
			if (isset ( Router::$sticked [$module] ) && preg_match ( '#^https?://.+#i', BASE_URL )) {
				$host = $_SERVER ['HTTP_HOST'];
				$dhost = parse_url ( BASE_URL, PHP_URL_HOST );
				if ($host != $dhost) {
					return $this->dispatchPage ( $do, false );
				}
			}
			$module = strtolower ( Router::$URL2APP [$module] );
		} else if (isset ( Router::$APP2URL [$module] )) {
			return $this->dispatchPage ( $do, $dispath );
		}
		if (! isset ( $__kissgo_apps [$module] )) {
			return $this->dispatchPage ( $do, $dispath );
		}
		
		$app = RtCache::get ( 'app@' . md5($do), true );
		if (! $app) {
			$action = strtolower ( $action );
			$app = $this->findApp ( $module, $action, $pms );
			if ($app) {
				list ( $controllerClz, $action, $pms ) = $app;
				RtCache::add ( 'app@' . md5($do), $app, true );
			}
		} else {
			list ( $controllerClz, $action, $pms, $f ) = $app;
			include $f;
			unset ( $f );
			define ( 'APPDIR', MODULES_PATH . $module . DS );
		}
		if ($app) {
			try {
				$res = Response::getInstance ();
				$req = Request::getInstance ( true );
				if (! is_subclass_of2 ( $controllerClz, 'NonSessionController' )) {
					$req->startSession ();
				}
				$rm = strtolower ( $_SERVER ['REQUEST_METHOD'] );
				$clz = new $controllerClz ( $req, $res );
				if ($action != 'index') {
					if (method_exists ( $clz, $action . '_' . $rm )) {
						$action = $action . '_' . $rm;
					} else if (! method_exists ( $clz, $action )) {
						array_unshift ( $pms, $action );
						$action = 'index';
					}
				}
				if (method_exists ( $clz, $action . '_' . $rm )) {
					$action = $action . '_' . $rm;
				}
				if (method_exists ( $clz, $action )) {
					$ref = new ReflectionObject ( $clz );
					$method = $ref->getMethod ( $action );
					$params = $method->getParameters ();
					if (count ( $params ) < count ( $pms )) {
						$this->dispatch ( $do );
						Response::respond ( 404 );
					}
					$clz->preRun ( $action );
					$args = array ();
					if ($params) {
						$idx = 0;
						foreach ( $params as $p ) {
							$name = $p->getName ();
							$def = isset ( $pms [$idx] ) ? $pms [$idx] : ($p->isDefaultValueAvailable () ? $p->getDefaultValue () : null);
							$value = rqst ( $name, $def, true );
							$args [] = $value;
							$idx ++;
						}
					}
					$this->dispacthing_url = $do;
					$view = call_user_func_array ( array ($clz,$action ), $args );
					if ($view instanceof SmartyView) {
						$view->setRelatedPath ( $module . '/views/' );
					}
					// postRun可以返回一个新的View用来代替之前的view.
					$postView = $clz->postRun ( $view );
					if ($postView) {
						$view = $postView;
					}
					$res->output ( $view );
				} else {
					$this->dispatch ( $do );
					Response::respond ( 404 );
				}
			} catch ( ReflectionException $e ) {
				$this->showErrorPage ( $e->getMessage () );
			}
		} else {
			return $this->dispatchPage ( $do, $dispath );
		}
	}
	/**
	 * 查找app处理器.
	 *
	 * @param string $module        	
	 * @param string $action        	
	 * @param array $params        	
	 */
	private function findApp($module, $action, $params) {
		if (is_numeric ( $action )) {
			array_unshift ( $params, $action );
			$action = 'index';
		}
		if ($action != 'index') {
			$controllerClz = ucfirst ( $action ) . 'Controller';
			$controller_file = MODULES_PATH . $module . DS . 'controllers' . DS . $controllerClz . '.php';
			$files [] = array ($controller_file,$controllerClz,'index' );
			
			$controllerClz = ucfirst ( $module ) . 'Controller';
			$controller_file = MODULES_PATH . $module . DS . 'controllers' . DS . $controllerClz . '.php';
			$files [] = array ($controller_file,$controllerClz,$action );
			
			foreach ( $files as $file ) {
				list ( $controller_file, $controllerClz, $action ) = $file;
				if (is_file ( $controller_file )) {
					include $controller_file;
					if (class_exists ( $controllerClz ) && is_subclass_of2 ( $controllerClz, 'Controller' )) {
						if ($action == 'index' && count ( $params ) > 0) {
							$action = array_shift ( $params );
						}
						define ( 'APPDIR', MODULES_PATH . $module . DS );
						return array ($controllerClz,$action,$params,$controller_file );
					} else {
						$controllerClz = $module . '\controllers\\' . $controllerClz;
						if (class_exists ( $controllerClz ) && is_subclass_of2 ( $controllerClz, 'Controller' )) {
							if ($action == 'index' && count ( $params ) > 0) {
								$action = array_shift ( $params );
							}
							define ( 'APPDIR', MODULES_PATH . $module . DS );
							return array ($controllerClz,$action,$params,$controller_file );
						}
					}
				}
			}
		} else {
			$controllerClz = ucfirst ( $module ) . 'Controller';
			$controller_file = MODULES_PATH . $module . DS . 'controllers' . DS . $controllerClz . '.php';
			if (is_file ( $controller_file )) {
				include $controller_file;
				if (class_exists ( $controllerClz ) && is_subclass_of2 ( $controllerClz, 'Controller' )) {
					define ( 'APPDIR', MODULES_PATH . $module . DS );
					return array ($controllerClz,$action,$params,$controller_file );
				} else {
					$controllerClz = $module . '\controllers\\' . $controllerClz;
					if (class_exists ( $controllerClz ) && is_subclass_of2 ( $controllerClz, 'Controller' )) {
						define ( 'APPDIR', MODULES_PATH . $module . DS );
						return array ($controllerClz,$action,$params,$controller_file );
					}
				}
			}
		}
		return false;
	}
	/**
	 * 将请求映射到页面（CMS管理）.
	 *
	 * @param string $url        	
	 */
	private function dispatchPage($url, $dispatch = true) {
		global $__kissgo_apps;
		if (! $dispatch) {
			$this->current_url = '';
			Response::respond ( 404, $url );
		}
		$origin_url = $url;
		$this->parsed_url = $parsed_url = $this->parseURL ( $url );
		$res = Response::getInstance ();
		$app = cfg ( 'default_app' );
		if ($dispatch && $app && (empty ( $url ) || $url == 'index.html')) {
			$this->current_url = '';
			if (isset ( $__kissgo_apps [$app] )) {
				$do = isset ( self::$APP2URL [$app] ) ? self::$APP2URL [$app] : $app;
				return $this->route ( $do, false );
			} else {
				Response::respond ( 404 );
			}
		}
		$this->checkDomain ( $origin_url );
		if (empty ( $url ) || $parsed_url == 'index.html') {
			// 直接使用index.tpl渲染页面内容
			$this->current_url = 'index.html';
			$data ['title'] = cfg ( 'site_title' );
			$data ['keywords'] = cfg ( 'keywords' );
			$data ['description'] = cfg ( 'description' );
			$data = apply_filter ( 'on_render_homepage', $data );
			if ($data) {
				$this->current_page_data = $data;
				$this->current_page_data ['_cpn'] = 1;
				if (isset ( $data ['template_file'] )) {
					$tpl = $data ['template_file'];
				} else {
					$tpl = 'index.tpl';
				}
				
				$data ['mf_page_data'] = $data;
				$res->output ( template ( $tpl, $data ) );
			} else {
				Response::respond ( 404, $origin_url );
			}
		} else if (preg_match ( '#\.(css|gif|jpg|jpeg|png|bmp)$#i', $url )) {
			Response::respond ( 404, $origin_url );
		} else {
			$this->dispatch ( $url, $parsed_url );
			if ($app) {
				$this->current_url = '';
				if (isset ( $__kissgo_apps [$app] )) {
					$urlm = isset ( self::$APP2URL [$app] ) ? self::$APP2URL [$app] : $app;
					$url = $urlm . '/' . $origin_url;
					$url = $this->parseURL ( $url );
					return $this->route ( $url, false );
				} else {
					Response::respond ( 404 );
				}
			} else {
				$this->current_url = '';
				Response::respond ( 404, $origin_url );
			}
		}
		$res->close ( false );
		exit ();
	}
	public function getParsedURL() {
		return $this->url_info;
	}
	/**
	 * 分发一个确定的页面.
	 *
	 * @param string $url        	
	 * @param string $parsed_url        	
	 */
	public function dispatch($url = false, $parsed_url = false) {
		if (! $url) {
			$url = $this->dispacthing_url;
		}
		if (! $url) {
			return;
		}
		$this->current_url = $url;
		if (! $parsed_url) {
			$parsed_url = $this->parseURL ( $url );
		}
		$this->parsed_url = $parsed_url;
		$page = apply_filter ( 'get_page_data', null, $this->decodeURL ( $parsed_url ) );
		if ($page) {
			$page = apply_filter ( 'on_render_page', $page );
			if ($page) {
				$this->current_page_data = $page->getFields ();
				$tpl = get_prefer_tpl ( $this->current_page_data ['template_file'] );
				$this->current_page_data ['_cpn'] = $this->current_page + 1;
				if ($this->url_info ['suffix'] == '.xml') {
					$headers = array ('Content-Type' => 'text/xml' );
				} else if ($this->url_info ['suffix'] == '.jsp') {
					$headers = array ('Content-Type' => 'application/javascript' );
				} else if ($this->url_info ['suffix'] == '.json') {
					$headers = array ('Content-Type' => 'application/json' );
				} else {
					$headers = array ('Content-Type' => 'text/html' );
				}
				if (isset ( $this->current_page_data ['http_headers'] )) {
					$headers = array_merge ( $headers, $this->current_page_data ['http_headers'] );
				}
				$this->current_page_data ['mf_page_data'] = $this->current_page_data;
				$res = Response::getInstance ();
				$res->output ( template ( $tpl, $this->current_page_data, $headers ) );
				$res->close ( false );
				exit ();
			}
		}
	}
	/**
	 * 获取当前请求参数。
	 *
	 * @return ArrayAccess
	 */
	public function getRequestArgs() {
		return $this->request_args;
	}
	/**
	 * 页面分页格式为 1=>aaaa.html;2=> aaaa_2.html.
	 *
	 * @param string $url        	
	 * @return mixed
	 */
	private function parseURL($url, $return = false) {
		if (rqset ( '_cpn' )) {
			$this->current_page = irqst ( '_cpn' );
		} else {
			$this->current_page = 0;
		}
		$this->url_info ['orgin_url'] = $url;
		if (preg_match ( '#(.+?)(_([\d]+))?(\.(s?html?|xml|jsp|json))$#', $url, $m )) {
			// url包括下划线加数字，都时系统允许下划线加数字这种形势的url。
			if ($m [3] && bcfg ( 'allow_dash@cms' ) && dbselect ()->from ( '{cms_page}' )->where ( array ('url_key' => md5 ( $url ) ) )->exist ( 'id' )) {
				$m [1] .= '_' . $m [3];
				$m [3] = '';
			}
			
			if (! rqset ( '_cpn' )) {
				$this->current_page = intval ( $m [3] > 0 ? $m [3] - 1 : 0 );
			}
			$url = $this->escapeURL ( $m [1] . $m [4], $m [4] );
			if ($return) {
				$url = array ('prefix' => $m [1],'suffix' => $m [4],'orgin' => $url );
			} else {
				$this->url_info ['prefix'] = $m [1];
				$this->url_info ['suffix'] = strtolower ( $m [4] );
				$this->url_info ['orgin'] = $url;
			}
		} else {
			$url = $this->escapeURL ( trim ( $url, '/' ) );
			if ($return) {
				$url = array ('prefix' => $url . '/index','suffix' => '.html','orgin' => $url );
			} else {
				$this->url_info ['prefix'] = $url . '/index';
				$this->url_info ['suffix'] = '.html';
				$this->url_info ['orgin'] = $url;
			}
		}
		
		return $url;
	}
	/**
	 * 使用urlencode处理URL的值，并生成参数.
	 *
	 * @param string $url        	
	 * @param string $suffix        	
	 * @return string
	 */
	private function escapeURL($url, $suffix = false) {
		$urls = explode ( '/', $url );
		$cnt = count ( $urls );
		$this->request_args = array ();
		if ($cnt > 0) {
			foreach ( $urls as $k => $u ) {
				$this->request_args [$k] = $u;
				$urls [$k] = urlencode ( $u );
			}
			if ($suffix) {
				$this->request_args [$k] = rtrim ( $this->request_args [$k], $suffix );
			}
		}
		$query_str = $_SERVER ['QUERY_STRING'];
		if ($query_str) {
			parse_str ( $query_str, $args );
			unset ( $args ['_url'], $args ['preview'] );
			if ($args) {
				$this->request_args = array_merge_recursive ( $this->request_args, $args );
			}
		}
		return implode ( '/', $urls );
	}
	private function decodeURL($url) {
		$urlx = explode ( '/', $url );
		$chunk = array ();
		foreach ( $urlx as $u ) {
			$chunk [] = urldecode ( $u );
		}
		return implode ( '/', $chunk );
	}
	private function checkDomain($origin_url) {
		$mobi_domain = cfg ( 'mobi_domain' );
		if ($mobi_domain && $mobi_domain == REAL_HTTP_HOST) {
			return;
		}
		$dhost = cfg ( 'cms_url@cms' );
		if ($dhost) {
			$dhost = parse_url ( $dhost, PHP_URL_HOST );
			if ($dhost) {
				$host = REAL_HTTP_HOST;
				if ($dhost != $host) {
					$host = strstr ( $host, '.' );
					$dhost = strstr ( $dhost, '.' );
					if ($dhost != $host) {
						$this->current_url = '';
						Response::respond ( 404, $origin_url );
					}
				}
			}
		}
	}
	/**
	 * 显示错误页面.
	 *
	 * @param string $error        	
	 */
	private function showErrorPage($error) {
		if (DEBUG == DEBUG_DEBUG) {
			die ( $error );
		} else {
			Response::respond ( 404 );
		}
	}
}