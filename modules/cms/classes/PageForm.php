<?php
/**
 * 页面
 * @author Guangfeng
 *
 */
class PageForm {
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
		$form ['chunk'] = array ('type' => 'int' );
		$form ['content'] = array ('filter' => array ('PageForm','getContent' ) );
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
		$form ['publish_time'] = array ('filter' => array ('PageForm','getPublishTime' ) );
		$form ['redirect'] = array ('rules' => array ('required(flag_j:checked)' => '请填写要跳转到的URL','url' => '跳转到的URL不合法.' ) );
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
		$form ['topic'] = array ('type' => 'int' );
		$form ['url'] = array ('scope' => 'PageForm','rules' => array ('callback(#checkURL,id)' => 'URL已经存在.' ) );
		$form ['view_count'] = array ('rules' => array ('digits' => '请输入正确的数字.' ) );
		$form ['related_pages'] = array ();
		$form ['description'] = array ();
		$form ['img_follow'] = array ();
		$form ['img_pagination'] = array ('type' => 'bool' );
		$form ['img_next_page'] = array ();
		$form ['expire'] = array ('rules' => array ('regexp(/^(\-1|0|[1-9]\d*)$/)' => '请填写正确的缓存时间.' ) );
	}
	/**
	 * 取发布时间，如果用户手动设置了的话.
	 *
	 * @param DynamicForm $form        	
	 * @param string $name        	
	 * @param string $value        	
	 * @return mixed
	 */
	public static function getPublishTime($form, $name, $value) {
		$date = rqst ( 'publish_date' );
		if (empty ( $date )) {
			return false;
		}
		if (! empty ( $value )) {
			return strtotime ( $date . ' ' . $value . ':00' );
		}
		return false;
	}
	/**
	 *
	 * @param DynamicForm $form        	
	 * @param unknown $name        	
	 * @param unknown $value        	
	 */
	public static function getContent($form, $name, $value) {
		$flag_j = $form->getValue ( 'flag_j' );
		if ($flag_j) {
			return $form->getValue ( 'redirect' );
		} else {
			return $value;
		}
	}
	/**
	 * 检测URL是否重复.
	 *
	 * @param string $value        	
	 * @param array $data        	
	 * @param string $message        	
	 * @return Ambigous <string, string, unknown>|boolean
	 */
	public static function checkURL($value, $data, $message) {
		if (empty ( $value )) {
			return true;
		}
		$rst = dbselect ( 'id' )->from ( '{cms_page}' );
		$where ['url_key'] = md5 ( $value );
		if (! empty ( $data ['id'] )) {
			$where ['id !='] = $data ['id'];
		}
		$rst->where ( $where );
		if ($rst->count ( 'id' ) > 0) {
			return $message;
		}
		return true;
	}
	public static function checkTitle($value, $data, $message) {
		if (empty ( $value )) {
			return true;
		}
		$value = trim ( $value );
		$rst = dbselect ( 'id' )->from ( '{cms_page}' );
		$where ['title'] = $value;
		$where ['deleted'] = 0;
		if (! empty ( $data ['id'] )) {
			$where ['id !='] = $data ['id'];
		}
		$rst->where ( $where );
		if ($rst->count ( 'id' ) > 0) {
			return $message;
		}
		return true;
	}
	public static function checkTitle2($value, $data, $message) {
		if (empty ( $value )) {
			return true;
		}
		$value = trim ( $value );
		$rst = dbselect ( 'id' )->from ( '{cms_page}' );
		$where ['title2'] = $value;
		$where ['deleted'] = 0;
		if (! empty ( $data ['id'] )) {
			$where ['id !='] = $data ['id'];
		}
		$rst->where ( $where );
		if ($rst->count ( 'id' ) > 0) {
			return $message;
		}
		return true;
	}
}