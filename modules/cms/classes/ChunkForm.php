<?php
/**
 * 碎片表单.
 * @author Guangfeng
 *
 */
class ChunkForm extends AbstractForm {
	private $catelog = array ('rules' => array ('required' => '请选择分类','regexp(/^[0-9]+$/)' => '非法的分类编号.' ) );
	private $id = array ('rules' => array ('regexp(/^[0-9]+$/)' => '非法的编号.' ) );
	private $name = array ('rules' => array ('required' => '碎片名不能为空.' ) );
	private $keywords = array ('rules' => array ('required' => '关键词不能为空.' ) );
	private $html = array ('rules' => array ('required' => '代码碎片不能为空.' ) );
	private $istpl = array ('type' => 'bool' );
	private $inline = array ('type' => 'bool' );
}
