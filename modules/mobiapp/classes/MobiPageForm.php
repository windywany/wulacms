<?php
class MobiPageForm extends AbstractForm {
	private $id = array ('id' => 'mobi_page_id','widget' => 'hidden' );
	private $channel = array ('id' => 'mobi_channel','group' => 1,'col' => 6,'label' => '栏目','widget' => 'select','rules' => array ('required' => '请选择栏目','callback(@checkChannel,id)' => '栏目不存在或无权限' ) );
	private $list_view = array ('id' => 'mobi_list_view','group' => 1,'col' => 6,'label' => '布局样式','widget' => 'select','rules' => array ('required' => '请选择布局样式' ) );
	/*
	 * (non-PHPdoc) @see AbstractForm::init_form_fields()
	 */
	protected function init_form_fields($data, $value_set) {
		$_lvs = MobiListView::getListViews ();
		$lvs = array ('=请选择布局样式' );
		
		foreach ( $_lvs as $id => $lv ) {
			$lvs [] = $id . '=' . $lv ['name'];
		}
		
		$this->__form_fields ['list_view']->setOptions ( array ('defaults' => implode ( "\n", $lvs ) ) );
		$channels = array ('=请选择栏目' );
		
		if ($value_set && $data ['channels']) {
			foreach ( $data ['channels'] as $id => $name ) {
				$channels [] = $id . '=' . $name;
			}
		}
		
		$this->__form_fields ['channel']->setOptions ( array ('defaults' => implode ( "\n", $channels ) ) );
	}
	public function checkChannel($value, $data, $msg) {
		if (! $data ['id']) {
			return '页面不存在';
		}
		if (! icando ( 'm_' . $value . ':mobi/ch' )) {
			return '无权限操作此栏目内容';
		}
		$page = dbselect ( 'channel,title,title2,description,model' )->from ( '{cms_page}' )->where ( array ('id' => $data ['id'] ) )->get ( 0 );
		if ($page) {
			$binds = dbselect ( 'MCH.refid,MCH.name' )->from ( '{mobi_channel_binds} AS MCB' )->join ( '{mobi_channel} AS MCH', 'MCB.mobi_refid = MCH.refid' )->where ( array ('MCB.cms_refid' => $page ['channel'] ) )->toArray ( 'name', 'refid' );
			return isset ( $binds [$value] ) ? true : '栏目不可用';
		} else {
			return '页面不存在';
		}
	}
}