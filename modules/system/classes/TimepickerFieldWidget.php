<?php
class TimepickerFieldWidget implements IFieldWidget {
	public function getDataProvidor($options) {
		return new EmptyDataProvidor ();
	}
	public function getType() {
		return 'time';
	}
	public function getName() {
		return '时间选择控件';
	}
	public function render($definition, $cls = '') {
		$value = $definition ['value'];
		$name = $definition ['name'];
		$id = isset ( $definition ['id'] ) ? $definition ['id'] : $name;
		return '<label class="input">
					<i class="icon-append fa fa-clock-o"></i>
					<input type="text" name="' . $name . '"
					data-widget="nuiTimepicker"
					id="' . $id . '" value="' . html_escape ( $value ) . '"/>
				</label>';
	}
}