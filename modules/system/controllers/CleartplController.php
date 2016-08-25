<?php
class CleartplController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('index' => 'cc:system' );
	public function index() {
		rmdirs ( TMP_PATH . '#themes_c' );
		rmdirs ( TMP_PATH . '#tpls_c' );
		rmdirs ( TMP_PATH . 'cache' );
		fire ( 'on_clear_tpl_cache' );
		return NuiAjaxView::ok ( '模板缓存已清除.' );
	}
}
