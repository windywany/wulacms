<?php
class NaviMenuForm extends AbstractForm {
	private $id = array ('rules' => array ('regexp(/^[0-9]+$/)' => '非法的编号.' ) );
	private $upid = array ('rules' => array ('required' => '请选择上级菜单' ) );
	private $name = array ('rules' => array ('required' => '请填写菜单标题' ) );
	private $url = array ('rules' => array ('required(page_id:blank)' => '请填写菜单标题' ) );
	private $navi;
	private $target;
	private $hidden;
	private $sort = array ('rules' => array ('regexp(/^[0-9]{1,3}$/)' => '请填写正确的序号(0-999).' ) );
	private $page_id = array ('rules' => array ('regexp(/^[0-9]*$/)' => '非法的页面编号.' ) );
}