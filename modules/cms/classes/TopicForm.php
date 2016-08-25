<?php
/**
 * 页面
 * @author Guangfeng
 *
 */
class TopicForm {
	/**
	 *
	 * @param DynamicForm $form        	
	 */
	public static function init($form) {
		$form ['allow_comment'] = array ('type' => 'bool' );
		$form ['author'] = array ();
		if (cfg ( 'tag_empty@cms', '1' ) == '0') {
			$form ['tag'] = array ('rules' => array ('required' => '请填写标签' ) );
		} else {
			$form ['tag'] = array ();
		}
		$form ['channel'] = array ('rules' => array ('required' => '请选择栏目' ) );
		$form ['chunk'] = array ('default' => 0 );
		$form ['content'] = array ();
		$form ['flag_a'] = array ('type' => 'bool' );
		$form ['flag_b'] = array ('type' => 'bool' );
		$form ['flag_c'] = array ('type' => 'bool' );
		$form ['flag_h'] = array ('type' => 'bool' );
		$form ['flag_j'] = array ('type' => 'bool' );
		$form ['id'] = array ('rules' => array () );
		$form ['image'] = array ();
		$form ['keywords'] = array ();
		$form ['model'] = array ();
		$form ['page_type'] = array ();
		$form ['publish_time'] = array ();
		$form ['source'] = array ();
		$form ['template_file'] = array ('rules' => array ('regexp(/^[a-z0-9][a-z0-9_\/\-]*\.tpl$/i)' => '模板文件名格式不正确.' ) );
		$form ['title'] = array ('scope' => 'PageForm','rules' => array ('required' => '请填写标题' ) );
		if (cfg ( 'title_repeatable@cms', '1' ) == '0') {
			$form->getField ( 'title' )->addValidate ( 'callback(#checkTitle,id)', '标题已经存在' );
		}
		$form ['title2'] = array ('scope' => 'PageForm' );
		if (cfg ( 'title2_repeatable@cms', '1' ) == '0') {
			$form->getField ( 'title2' )->addValidate ( 'callback(#checkTitle2,id)', '短标题已经存在' );
		}
		$form ['title_color'] = array ();
		$form ['topic'] = array ('default' => 0 );
		$form ['url'] = array ('scope' => 'PageForm','rules' => array ('regexp(/^[\{a-z0-9][\{\}a-z0-9_\/\-]*\.s?html?$/i)' => 'URL格式不正确.','callback(#checkURL,id)' => 'URL已经存在.' ) );
		$form ['view_count'] = array ('default' => 0 );
		$form ['description'] = array ();
		$form ['related_pages'] = array ();
		$form ['expire'] = array ('rules' => array ('regexp(/^(\-1|0|[1-9]\d*)$/)' => '请填写正确的缓存时间.' ) );
	}
}