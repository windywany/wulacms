<?php
/**
 * 区块内容表单.
 * @author Guangfeng
 *
 */
class BlockItemForm extends AbstractForm {
	private $id = array ('rules' => array ('regexp(/^[0-9]+$/)' => '非法的编号.' ) );
	private $block = array ('rules' => array ('required' => '请选择分类','regexp(/^[0-9]+$/)' => '非法的分类编号.' ) );
	private $title = array ('rules' => array ('required' => '标题不能为空.' ) );
	private $url = array ('rules' => array ('required' => '图片URL不能为空.' ) );
	private $image = array ('rules' => array ('regexp(/^[_a-z0-9\-][_a-z0-9:\-\.\\\\\\/]+\.(jpg|gif|png|jpeg)$/i)' => '图片URL不合法.' ) );
	private $sort = array ('rules' => array ('regexp(/^(0|[1-9]\d{0,2})$/)' => '0-999中的一个数值' ) );
	private $page_id = array ('rules' => array ('regexp(/^(0|[1-9]\d*)$/)' => '请填写一个页面编号' ) );
	private $description;
}