<?php
/**
 * 自定义文件字段输入组件.
 * @author Guangfeng
 *
 */
class TextFieldWidget implements IFieldWidget {
	/*
	 * (non-PHPdoc) @see IFieldWidget::getName()
	 */
	public function getName() {
		return '单行文本';
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidget::getType()
	 */
	public function getType() {
		return 'text';
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidget::render()
	 */
	public function render($definition, $cls = '') {
		$id = isset ( $definition ['id'] ) ? $definition ['id'] : $definition ['name'];
		$pl = isset ( $definition ['placeholder'] ) ? 'placeholder="' . $definition ['placeholder'] . '" ' : '';
		$readonly = isset ( $definition ['readonly'] ) ? ' readonly="readonly" ' : '';
		$disabled = isset ( $definition ['disabled'] ) ? ' disabled="disabled" ' : '';
		
		$html [] = '<label class="input">';
		$html [] = '<input id="' . $id . '" type="text" ' . $pl . $readonly . $disabled . ' name="' . $definition ['name'] . '" value="' . html_escape ( $definition ['value'] ) . '" class="' . $cls . '"/>';
		$html [] = '</label>';
		return implode ( '', $html );
	}
	public function getDataProvidor($options) {
		return new EmptyDataProvidor ();
	}
}