<?php
/**
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Windywany
 * @package kissgo
 * @date 12-9-18 下午9:17
 * $Id$
 */
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 * 可将自身绘制成html片断的类.
 *
 * @author Guangfeng Ning <windywany@gmail.com>
 *        
 */
interface Renderable {
	/**
	 * 将自身绘制成html片断.
	 *
	 * @return string html fragment.
	 */
	public function render();
}
/**
 * 支持布局的页面.
 *
 * @author Guangfeng Ning <windywany@gmail.com>
 *        
 */
interface ILayoutedPage {
	/**
	 * 样式.
	 */
	function getStyles();
	/**
	 * 小部件容器.
	 */
	function containers();
}
/**
 * 默认的布局模板页.
 *
 * @author Guangfeng Ning <windywany@gmail.com>
 *        
 */
abstract class DefaultLayoutedPage implements ILayoutedPage {
	public function getStyles() {
		return array ();
	}
	/*
	 * (non-PHPdoc) @see ILayoutedPage::layout()
	 */
	public function layout($hidden = null) {
		$view = $this->getView ();
		$containers = $this->containers ();
		// 要隐藏的小部件容器.
		$hidden = func_get_args ();
		$shows = array ();
		$data ['layout_body_classes'] = '';
		if (is_array ( $hidden ) && $hidden) {
			$data ['layout_body_classes'] = 'no-' . implode ( ' no-', $hidden );
			foreach ( $containers as $c ) {
				$id = $c [0] . '_' . $c [2];
				if (! in_array ( $c [2], $hidden )) {
					$shows [$id] = $c;
				}
			}
		} else if (! $hidden) {
			$shows = $containers;
		}
		if ($shows) {
			$ld_containers = KsWidgetContainer::loads ( $shows );
			if ($ld_containers) {
				foreach ( $ld_containers as $id => $container ) {
					$data [$id] = $container;
					$container->prepareResources ( $view );
				}
			}
		}
		$view->assign ( $data );
		return $view;
	}
	/**
	 * 页面使用的模板.
	 *
	 * @return SmartyView
	 */
	public abstract function getView();
}
/**
 * 视图基类
 *
 * 用于定义模板的绘制和头部输出.
 *
 * @author Guangfeng Ning <windywany@gmail.com> 2010-11-14 12:25
 * @version 1.0
 * @since 1.0
 * @package view
 */
abstract class View implements ArrayAccess, Renderable {
	protected $tpl = '';
	protected $data;
	protected $relatedPath;
	protected $headers = array ();
	protected $sytles = array ();
	protected $scripts = array ('head' => array (),'foot' => array () );
	protected $title = null;
	/**
	 *
	 * @param string|array $data        	
	 * @param string $tpl        	
	 * @param array $headers        	
	 */
	public function __construct($data = array(), $tpl = '', $headers = array()) {
		if (empty ( $data )) {
			$this->tpl = str_replace ( '/', DS, $tpl );
			$this->data = array ();
		} else if (is_array ( $data )) {
			$this->tpl = str_replace ( '/', DS, $tpl );
			$this->data = $data;
		} else if (is_string ( $data )) {
			$this->tpl = str_replace ( '/', DS, $data );
			$this->data = array ();
		} else {
			trigger_error ( 'no template file!' );
		}
		
		if (is_array ( $headers )) {
			$this->headers = $headers;
		}
	}
	public function offsetExists($offset) {
		return isset ( $this->data [$offset] );
	}
	public function offsetGet($offset) {
		return $this->data [$offset];
	}
	public function offsetSet($offset, $value) {
		$this->data [$offset] = $value;
	}
	public function offsetUnset($offset) {
		unset ( $this->data [$offset] );
	}
	public function setTitle($title) {
		$this->title = $title;
	}
	public function getTitle() {
		return $this->title;
	}
	public function assign($data, $value = null) {
		if (is_array ( $data )) {
			$this->data = array_merge_recursive ( $this->data, $data );
		} else if ($data) {
			$this->data [$data] = $value;
		}
	}
	public function addStyle($file) {
		if (is_array ( $file )) {
			foreach ( $file as $f ) {
				if (! in_array ( $f, $this->sytles )) {
					$this->sytles [] = $f;
				}
			}
		} else if (! in_array ( $file, $this->sytles )) {
			$this->sytles [] = $file;
		}
	}
	public function getStyles($view = null) {
		if ($view instanceof View) {
			$view->sytles = $this->sytles;
		}
		return $this->sytles;
	}
	public function addScript($file, $foot = false) {
		if ($foot) {
			if (is_array ( $file )) {
				foreach ( $file as $f ) {
					if (! in_array ( $f, $this->scripts ['foot'] )) {
						$this->scripts ['foot'] [] = $f;
					}
				}
			} else if (! in_array ( $file, $this->scripts ['foot'] )) {
				$this->scripts ['foot'] [] = $file;
			}
		} else {
			if (is_array ( $file )) {
				foreach ( $file as $f ) {
					if (! in_array ( $f, $this->scripts ['head'] )) {
						$this->scripts ['head'] [] = $f;
					}
				}
			} else if (! in_array ( $file, $this->scripts ['head'] )) {
				$this->scripts ['head'] [] = $file;
			}
		}
	}
	public function getScripts($type = null) {
		if ($type instanceof View) {
			$type->scripts = $this->scripts;
		}
		if ($type == 'foot') {
			return $this->scripts ['foot'];
		} else if ($type == 'head') {
			return $this->scripts ['head'];
		} else {
			return $this->scripts;
		}
	}
	public function getData() {
		return $this->data;
	}
	public function activeMenu($id) {
		if ($id instanceof View) {
			$id->data ['_view_actived_menu'] = $this->data ['_view_actived_menu'];
			return;
		}
		
		if ($id) {
			$this->data ['_view_actived_menu'] = $id;
		}
	}
	
	/**
	 * set http response header
	 */
	public function echoHeader() {
		if (! empty ( $this->headers ) && is_array ( $this->headers )) {
			foreach ( $this->headers as $name => $value ) {
				@header ( "$name: $value", true );
			}
		}
		$this->setHeader ();
	}
	public function setRelatedPath($path) {
		if ($this->tpl && $this->tpl {0} == '@') {
			$this->tpl = substr ( $this->tpl, 1 );
		} else {
			$this->relatedPath = $path;
		}
	}
	/**
	 * 设置输出头
	 */
	protected function setHeader() {
	}
	/**
	 * 取一个Smarty实例.
	 *
	 * @return Smarty
	 */
	public static function getSmarty() {
		$smarty = new Smarty ();
		$smarty->addPluginsDir ( INCLUDES . 'vendors/smarty/user_plugins' );
		$smarty->compile_dir = TMP_PATH . 'tpls_c' . DS . '_inner'; // 模板编译目录
		$smarty->cache_dir = TMP_PATH . 'tpls_cache' . DS . '_inner'; // 模板缓存目录
		$smarty = apply_filter ( 'init_smarty_engine', $smarty );
		$smarty = apply_filter ( 'init_view_smarty_engine', $smarty );
		$smarty->assign ( '_SessionName', get_session_name () );
		$smarty->assign ( '_SessionID', session_id () );
		return $smarty;
	}
}

/**
 * JSON视图
 *
 * 通过json_encode函数输出
 *
 * @author Guangfeng Ning <windywany@gmail.com> 2010-11-14 12:25
 * @version 1.0
 * @since 1.0
 * @package view
 */
class JsonView extends View {
	/**
	 *
	 * @param array|string $data        	
	 * @param array $headers        	
	 */
	public function __construct($data, $headers = array()) {
		parent::__construct ( $data, '', $headers );
	}
	
	/**
	 * 绘制
	 *
	 * @return string
	 */
	public function render() {
		return json_encode ( $this->data );
	}
	public function setHeader() {
		@header ( 'Content-type: application/json', true );
	}
}

/**
 * HTML视图
 *
 * 使用PHP 语法定义的HTML视图
 *
 * @author Guangfeng Ning <windywany@gmail.com> 2010-11-14 12:25
 * @version 1.0
 * @since 1.0
 * @package view
 */
class HtmlView extends View {
	/**
	 * 绘制
	 *
	 * @return string
	 */
	public function render() {
		$tpl = '';
		if (is_file ( $tpl )) {
			extract ( $this->data );
			@ob_start ();
			include $tpl;
			$content = @ob_get_contents ();
			@ob_end_clean ();
			return $content;
		} else {
			log_error ( 'The view template ' . $tpl . ' is not found' );
			return '';
		}
	}
	public function setHeader() {
		@header ( 'Content-Type: text/html' );
	}
}

/**
 * HTML视图
 *
 * 使用PHP 语法定义的HTML视图
 *
 * @author Guangfeng Ning <windywany@gmail.com> 2010-11-14 12:25
 * @version 1.0
 * @since 1.0
 * @package view
 */
class SimpleView extends View {
	/**
	 *
	 * @param array|string $data        	
	 */
	public function __construct($data) {
		parent::__construct ( array ($data ) );
	}
	
	/**
	 * 绘制
	 *
	 * @return string
	 */
	public function render() {
		return array_pop ( $this->data );
	}
	public function setHeader() {
		@header ( 'Content-type: text/plain; charset=utf-8', true );
	}
}
/**
 * 生成一个html tag.
 *
 * @param string $name        	
 * @return HtmlTagElm
 */
function dashboard_htmltag($name, $attrs = array()) {
	return new HtmlTagElm ( $name, $attrs );
}
// END OF FILE view.php