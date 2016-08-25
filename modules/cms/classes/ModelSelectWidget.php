<?php
/**
 * 内容模型选择框.
 * @author Guangfeng
 *
 */
class ModelSelectWidget implements IFieldWidget {
	
	/*
	 * (non-PHPdoc) @see IFieldWidget::getName()
	 */
	public function getName() {
		return '内容模型';
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidget::getType()
	 */
	public function getType() {
		return 'model_select';
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidget::render()
	 */
	public function render($definition, $cls = '') {
		$data = array ();
		if (empty ( $definition ['required'] )) {
			$data = array ('' => '-请选择内容模型-' );
		}
		dbselect ()->from ( '{cms_model}' )->treeWhere ( array ('deleted' => 0,'hidden' => 0 ) )->treeKey ( 'refid' )->treeOption ( $data );
		$html [] = '<label class="select"><select name="' . $definition ['name'] . '">';
		foreach ( $data as $key => $d ) {
			$html [] = '<option value="' . $key . '">' . $d . '</option>';
		}
		$html [] = '</select><i></i></label>';
		return implode ( '', $html );
	}
	public function getDataProvidor($options) {
		return new EmptyDataProvidor();
	}
}