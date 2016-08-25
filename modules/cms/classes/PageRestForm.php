<?php
class PageRestForm extends AbstractForm {
	private $id = array ('rules' => array ('regexp(//)' => '非法编号.' ) );
	private $author = array ();
	private $tag = array ();
	private $channel = array ('rules' => array ('required' => '请选择栏目','callback(@checkChannel)' => '栏目不存在.' ) );
	private $flag_a = array ('type' => 'bool' );
	private $flag_b = array ('type' => 'bool' );
	private $flag_c = array ('type' => 'bool' );
	private $flag_h = array ('type' => 'bool' );
	private $image;
	private $keywords;
	private $model = array ('rules' => array ('required' => '请填写模型.','callback(@checkModel)' => '栏目不存在.' ) );
	private $source;
	private $title = array ('rules' => array ('required' => '请填写标题' ) );
	private $url = array ('scope' => 'PageForm','rules' => array ('regexp(/^[\{a-z0-9][\{\}a-z0-9_\/\-]*\.s?html?$/i)' => 'URL格式不正确.','callback(#checkURL,id)' => 'URL已经存在.' ) );
	private $description = array ();
	private $img_follow = array ();
	private $img_pagination = array ('type' => 'bool' );
	private $img_next_page = array ();
	private $title_color = array ();
	private $related_pages = array ();
	public function checkChannel($value, $data, $message) {
		if (dbselect ()->from ( '{cms_channel}' )->where ( array ('refid' => $value,'default_model' => $data ['model'] ) )->exist ( 'id' )) {
			return true;
		}
		return $message;
	}
	public function checkModel($value, $data, $message) {
		if (dbselect ()->from ( '{cms_model}' )->where ( array ('refid' => $value ) )->exist ( 'id' )) {
			return true;
		}
		return $message;
	}
}