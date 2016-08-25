<?php
/**
 * 基于通行证的用户中心基类.
 * @author leo.
 *
 */
abstract class AbstractVipController extends Controller {
	/**
	 * 检测类型为vip的用户登录.
	 *
	 * @var unknown $checkUser
	 */
	protected $checkUser = array ('passport','vip' );
	/**
	 * 添加布局支持.
	 *
	 * {@inheritDoc}
	 *
	 * @see Controller::postRun()
	 */
	public function postRun(&$view) {
		if ($view instanceof SmartyView && $view->isInLayout ()) {
			$layout = apply_filter ( 'get_ucenter_layout', null );
			if ($layout && $layout instanceof SmartyView) {
				$layout->setRelatedPath ( MODULES_PATH );
				$layout ['user'] = $this->user;
				$layout ['workspace'] = $view;
				$view->getScripts ( $layout );
				$view->getStyles ( $layout );
				$view->activeMenu ( $layout );
				$view ['user'] = $this->user;
				$title = $view->getTitle ();
				if ($title) {
					$layout ['title'] = $title;
				}
				return $layout;
			}
		}
		return null;
	}
}