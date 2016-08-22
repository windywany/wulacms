<?php
/**
 * 页面
 * @author Guangfeng
 *
 */
class CPageForm {
	/**
	 *
	 * @param DynamicForm $form        	
	 */
	public static function init($form) {
		$form ['allow_comment'] = array ('type' => 'bool' );
		$form ['author'] = array ();
		$form ['tag'] = array ();
		$form ['chunk'] = array ('default' => 0 );
		$form ['content'] = array ();
		$form ['flag_a'] = array ('type' => 'bool' );
		$form ['flag_b'] = array ('type' => 'bool' );
		$form ['flag_c'] = array ('type' => 'bool' );
		$form ['flag_h'] = array ('type' => 'bool' );
		$form ['flag_j'] = array ('type' => 'bool' );
		$form ['is_tpl_page'] = array ('type' => 'bool' );
		$form ['id'] = array ('rules' => array () );
		$form ['keywords'] = array ();
		$form ['publish_time'] = array ();
		$form ['source'] = array ();
		$form ['template_file'] = array ('rules' => array ('required' => '模板文件不能空.','regexp(/^[a-z0-9][a-z0-9_\/\-]*\.tpl$/i)' => '模板文件名格式不正确.' ) );
		$form ['title'] = array ();
		$form ['title2'] = array ('rules' => array ('required' => '请填写页面名称' ) );
		$form ['title_color'] = array ();
		$form ['topic'] = array ('default' => 0 );
		$form ['url'] = array ('scope' => 'PageForm','rules' => array ('required' => '请填写URL','regexp(/^[^\s]+\.(s?html?|xml|jsp|json)$/i)' => 'URL格式不正确.','callback(#checkURL,id)' => 'URL已经存在.' ) );
		$form ['view_count'] = array ('default' => 0 );
		$form ['description'] = array ();
		$form ['related_pages'] = array ();
		$form ['expire'] = array ('rules' => array ('regexp(/^(\-1|0|[1-9]\d*)$/)' => '请填写正确的缓存时间.' ) );
		$form ['url_handler'] = array ();
	}
}