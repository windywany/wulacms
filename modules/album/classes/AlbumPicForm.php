<?php
class AlbumPicForm extends AbstractForm {
	private $id = array ('id' => 'pic_id','widget' => 'hidden','rules' => array ('required' => '相片编辑不能为空','regexp(/^[1-9]\d*$/)' => '非法的相片编号' ) );
	private $title = array ('group' => '1','col' => '9','label' => '名称','rules' => array ('required' => '请填写名称' ) );
	private $is_hot = array ('group' => '1','col' => '3','label' => '&nbsp;','default' => '','defaults' => "1=推荐",'widget' => 'checkbox' );
	private $note = array ('label' => '说明','widget' => 'textarea','row' => 3 );
}