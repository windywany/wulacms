<?php
class LayoutController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('index' => 'layout:system','index_post' => 'layout:system' );
	public function index($page, $theme) {
		if (empty ( $page ) || empty ( $theme )) {
			Response::showErrorMsg ( '页面或布局为空.' );
		}
		if (! class_exists ( $theme )) {
			Response::showErrorMsg ( '布局类不存在.' );
		}
		$themeClz = new $theme ();
		if (! $themeClz instanceof ILayoutedPage) {
			Response::showErrorMsg ( '布局类不是的ILayoutedPage的实现.' );
		}
		$data = array ();
		$data ['containers'] = $themeClz->containers ();
		if (! $data ['containers']) {
			Response::showErrorMsg ( '此布局不支持自定义.' );
		}
		$data ['layout_class'] = $theme;
		$data ['page_id'] = $page;
		$widgets = KsWidgetContainer::loads ( $data ['containers'], false );
		$data ['providors'] = KsWidgetContainer::getSupportedDataProvidors ();
		$data ['widgets'] = $widgets;
		foreach ( $data ['containers'] as $id => $c ) {
			$data ['containers_options'] [$c [2]] = $c [1];
		}
		$form = new KsWidgetForm ( array ('positions' => $data ['containers_options'] ) );
		$data ['newRender'] = new DefaultFormRender ( $form->buildWidgets ( array ('page' => $page ) ) );
		$data ['rules'] = $form->rules ();
		return view ( 'layout.tpl', $data );
	}
	public function index_post() {
		$form = new KsWidgetForm ();
		$form ['wid'] = array ('rules' => array ('required' => '必须填写' ) );
		$form ['sort'] = array ('rules' => array ('required' => '必须填写','regexp(/^\d+$/)' => '只能是数字' ) );
		$form ['hidden'] = array ('rules' => array ('regexp(/^(0|1)$/)' => '只能是0或1' ) );
		$data = $form->valid ();
		
		if ($data) {
			$vcls = $data ['viewcls'];
			$dcls = $data ['datacls'];
			$vlz = new $vcls ();
			if ($vlz instanceof KsWidgetView) {
				$vform = new DynamicForm ( null );
				$vlz->getConfigFields ( $vform );
				$vopts = $vform->valid ();
			}
			$dlz = new $dcls ();
			if ($dlz instanceof KsDataProvidor) {
				$dform = new DynamicForm ( null );
				$dlz->getConfigFields ( $dform );
				$dopts = $dform->valid ();
			}
			if ($dopts == false || $vopts === false) {
				return NuiAjaxView::error ( '数据检验失败' );
			} else {
				$data ['data_options'] = serialize ( $dopts );
				$data ['view_options'] = serialize ( $vopts );
				$data ['update_time'] = time ();
				$data ['update_uid'] = $this->user->getUid ();
				$wid = $data ['wid'];
				dbsave ( $data, array ('wid' => $wid ), 'wid' )->into ( '{widgets}' )->exec ();
				return NuiAjaxView::click ( '#refresh', '保存成功' );
			}
		} else {
			return NuiAjaxView::error ( '数据格式不正确,无法保存' );
		}
	}
	public function views($p = '') {
		$data ['more'] = false;
		$data ['results'] = array ();
		if ($p) {
			if (class_exists ( $p )) {
				$dataClz = new $p ();
				if ($dataClz instanceof KsDataProvidor) {
					$type = $dataClz->getDataType ();
					$views = KsWidgetContainer::getSupportedViews ();
					if ($views) {
						foreach ( $views as $id => $v ) {
							if (class_exists ( $id )) {
								$vcls = new $id ();
								if ($vcls instanceof KsWidgetView) {
									if ($type == $vcls->supportDataType ()) {
										$data ['results'] [] = array ('id' => $id,'text' => $v );
									}
								}
							}
						}
					}
				}
			}
		}
		return new JsonView ( $data );
	}
	public function add_post() {
		$form = new KsWidgetForm ();
		$data = $form->valid ();
		if ($data) {
			$time = time ();
			$data ['wid'] = 'wid_' . time () . rand_str ( 2 );
			$data ['update_time'] = $data ['create_time'] = $time;
			$data ['update_uid'] = $data ['create_uid'] = $this->user->getUid ();
			$data ['sort'] = 999;
			$data ['hidden'] = 0;
			$data ['data_options'] = $data ['view_options'] = '';
			$rst = dbinsert ( $data )->into ( '{widgets}' )->exec ();
			if ($rst [0]) {
				return NuiAjaxView::click ( '#refresh', '保存成功' );
			} else {
				return NuiAjaxView::error ( '无法新增小部件' );
			}
		} else {
			return NuiAjaxView::validate ( 'KsWidgetForm', '数据格式有错.', $form->getErrors () );
		}
	}
	public function del($wid) {
		dbdelete ()->from ( '{widgets}' )->where ( array ('wid' => $wid ) )->exec ();
		return NuiAjaxView::click ( '#refresh', '小部件已经删除.' );
	}
}