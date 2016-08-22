<?php
class PasswordFieldWidget implements IFieldWidget {
	/*
	 * (non-PHPdoc) @see IFieldWidget::getName()
	 */
	public function getName() {
		return '密码输入框';
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidget::getType()
	 */
	public function getType() {
		return 'password';
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
		$html [] = '<input id="' . $id . '" type="password" ' . $pl . $readonly . $disabled . ' name="' . $definition ['name'] . '" value="' . $definition ['value'] . '" class="' . $cls . '"/>';
		$html [] = '</label>';
		return implode ( '', $html );
	}
	/*
	 * (non-PHPdoc) @see IFieldWidget::getDataProvidor()
	 */
	public function getDataProvidor($options) {
		return new EmptyDataProvidor ();
	}
}