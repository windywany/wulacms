<?php
class TplFieldWidget extends EmptyDataProvidor implements IFieldWidget {
	public function getDataProvidor($options) {
		return $this;
	}
	public function getType() {
		return 'tpl';
	}
	public function getName() {
		return '模板文件选择器';
	}
	public function render($definition, $cls = '') {
		$url = tourl ( 'system/ajax/tpl' );
		$id = $definition ['id'];
		$name = $definition ['name'];
		
		$readonly = isset ( $definition ['readonly'] ) ? ' readonly="readonly" ' : '';
		$disabled = isset ( $definition ['disabled'] ) ? ' disabled="disabled" ' : '';
		$placeholder = isset ( $definition ['placeholder'] ) ? ' placeholder="' . $definition ['placeholder'] . '"' : '';
		return '<label class="input" for="' . $id . '">
											<input type="hidden"
											data-widget="nuiCombox"
											style="width:100%"
											data-source="' . $url . '"
											name="' . $name . '" id="' . $id . '" value="' . $definition ['value'] . '"' . $readonly . $disabled . $placeholder . '/>
										</label>';
	}
}