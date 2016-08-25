<?php
class HiddenFieldWidget implements IFieldWidget {
	public function getDataProvidor($options) {
		return new EmptyDataProvidor ();
	}
	public function getType() {
		return 'hidden';
	}
	public function getName() {
		return '隐藏域';
	}
	public function render($definition, $cls = '') {
		$id = isset ( $definition ['id'] ) ? $definition ['id'] : $definition ['name'];
		return '<input type="hidden" id="' . $id . '" name="' . $definition ['name'] . '" value="' . $definition ['value'] . '"/>';
	}
}