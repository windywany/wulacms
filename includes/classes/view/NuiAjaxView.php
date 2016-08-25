<?php
class NuiAjaxView extends JsonView {
	const CLOSE = 'close'; // close dialog or tab
	const REFRESH = 'refresh'; // refresh dialog or tab
	const REDIRECT = 'redirect'; // redirect to a new page
	const RELOAD = 'reload'; // reload reloadable widget(table,grid)
	const VALIDATE = 'validate'; // validate the form
	const CLICK = 'click'; // trigger a element's click event
	const UPDATE = 'update'; // 回调函数
	const CALLBACK = 'callback';
	const DIALOG = 'dialog';
	public static function reload($table, $message = '') {
		return NuiAjaxView::ok ( $message, 'reload', $table );
	}
	public static function click($element, $message = '') {
		return NuiAjaxView::ok ( $message, 'click', $element );
	}
	public static function validate($form, $message = '', $errors = null) {
		if ($errors) {
			$form = array ('form' => $form,'errors' => $errors );
		}
		return NuiAjaxView::error ( $message, 'validate', $form );
	}
	public static function callback($func, $args = array(), $message = '') {
		$args ['func'] = $func;
		return NuiAjaxView::ok ( $message, 'callback', $args );
	}
	public static function redirect($message, $url) {
		$args = $url;
		return NuiAjaxView::ok ( $message, 'redirect', $args );
	}
	public static function dialog($content, $title, $args = array()) {
		$data ['status'] = 200;
		$data ['message'] = false;
		$data ['cb'] = 'dialog';
		$args ['content'] = $content;
		$args ['title'] = $title;
		$data ['args'] = $args;
		return new NuiAjaxView ( $data );
	}
	public static function refresh($message) {
		return NuiAjaxView::ok ( $message, 'refresh' );
	}
	public static function ok($message = '', $callback = false, $args = array()) {
		$data ['status'] = 200;
		$data ['message'] = $message;
		$data ['cb'] = $callback;
		$data ['args'] = $args;
		return new NuiAjaxView ( $data );
	}
	public static function error($message = '', $callback = false, $args = array()) {
		$data ['status'] = 300;
		$data ['message'] = $message;
		$data ['cb'] = $callback;
		$data ['args'] = $args;
		return new NuiAjaxView ( $data );
	}
	public static function timeout($loginURL = '') {
		$data ['status'] = 301;
		$data ['loginURL'] = $loginURL ? $loginURL : tourl ( 'system/login' );
		return new NuiAjaxView ( $data );
	}
	public static function auth($message) {
		$data ['status'] = 401;
		$data ['message'] = $message;
		return new NuiAjaxView ( $data );
	}
	/**
	 * 关闭对话框或tab
	 *
	 * @param unknown $id        	
	 * @param unknown $message        	
	 * @return NuiAjaxView
	 */
	public static function close($id, $message) {
		$data ['status'] = 200;
		$data ['cb'] = 'close';
		$data ['message'] = $message;
		$data ['args'] = $id;
		return new NuiAjaxView ( $data );
	}
}