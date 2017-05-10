<?php
class MobiPageForm2 extends AbstractForm {
	private $id = array ('id' => 'mobi_page_id','widget' => 'hidden' );
	private $channel = array ('id' => 'mobi_channel','group' => 1,'col' => 8,'label' => '栏目','widget' => 'select','disabled' => true );
	private $list_view = array ('id' => 'mobi_list_view','group' => 1,'col' => 4,'label' => '布局样式','widget' => 'select','disabled' => true );
	private $title = array ('id' => 'mobi_title','group' => 2,'col' => 8,'placeholder' => '标题','rules' => array ('required' => '请填写标题' ) );
	private $page_view = array ('id' => 'mobi_page_view','group' => 2,'col' => 4,'widget' => 'select','placeholder' => '选择展示模板','rules' => array ('required' => '请选择展示模板' ) );
	/*
	private $view_url = array ('id' => 'mobi_view_url','placeholder' => '自定义URL','rules' => array ('url' => 'URL格式不正确' ) );
	*/
	private $flags = array ('widget' => 'checkbox' );
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
		
		$pageviews = array ();
		if ($value_set && $data ['pageviews']) {
			foreach ( $data ['pageviews'] as $id => $name ) {
				$pageviews [] = $id . '=' . $name;
			}
		}
		$this->__form_fields ['page_view']->setOptions ( array ('defaults' => implode ( "\n", $pageviews ) ) );
		
		$flags = MobiPageForm2::getPageFlags ();
		
		$_flags = array ();
		foreach ( $flags as $key => $f ) {
			$_flags [] = $key . '=' . $f;
		}
		
		$this->__form_fields ['flags']->setOptions ( array ('defaults' => implode ( "\n", $_flags ) ) );
		
		if ($value_set && $data ['listViewClz']) {
			$data ['listViewClz']->fillEditForm ( $this );
		}
		$this->addField ( 'desc', array ('id' => 'mobi_desc','placeholder' => '描述','widget' => 'textarea','row' => 2 ) );
	}
	public static function getPageFlags() {
		return apply_filter ( 'get_mobiapp_page_flags', array ('a' => '热门','c' => '推荐','h' => '头条','t' => '专题' ) );
	}
}
