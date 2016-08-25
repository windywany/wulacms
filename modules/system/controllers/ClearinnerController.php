<?php
/**
 * 清空内置缓存.
 * @author Guangfeng
 *
 */
class ClearinnerController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('index' => 'cc:system' );
	public function index() {
		RtCache::clear ();		
		return NuiAjaxView::refresh ( '清空内置缓存完成.' );
	}
}