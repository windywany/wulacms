<?php
class DatepickerFieldWidget implements IFieldWidget, IFieldWidgetDataProvidor {
	private $options;
	private static $data = array ('to' => '','from' => '','placeholder' => '' );
	public function getDataProvidor($options) {
		return $this;
	}
	public function getType() {
		return 'date';
	}
	public function getName() {
		return '日期选择控件';
	}
	public function render($definition, $cls = '') {
		$this->setOptions ( $definition ['defaults'] );
		$config = $this->getData ();
		$value = $definition ['value'];
		$name = $definition ['name'];
		$id = isset ( $definition ['id'] ) ? $definition ['id'] : $name;
		$html [] = '<label class="input"><i class="icon-append fa fa-calendar"></i>';
		$readonly = isset ( $definition ['readonly'] ) ? ' readonly="readonly" ' : '';
		$disabled = isset ( $definition ['disabled'] ) ? ' disabled="disabled" ' : '';
		if ($readonly || $disabled) {
			$html [] = '<input id="' . $id . '" ' . $readonly . $disabled;
		} else {
			$html [] = '<input id="' . $id . '" data-widget="nuiDatepicker"';
		}
		if ($config ['from']) {
			$html [] = ' data-range-from ="' . $config ['from'] . '"';
		}
		if ($config ['to']) {
			$html [] = ' data-range-to ="' . $config ['to'] . '"';
		}
		
		$html [] = ' type="text" placeholder="' . $config ['placeholder'] . '" name="' . $name . '" value="' . html_escape ( $value ) . '"/></label>';
		return implode ( '', $html );
	}
	/*
	 * (non-PHPdoc) @see IFieldWidgetDataProvidor::getData()
	 */
	public function getData($option = false) {
		if (is_string ( $this->options )) {
			$datax = @json_decode ( $this->options, true );
		} else {
			$datax = $this->options;
		}
		if ($datax) {
			$datax = array_merge ( self::$data, $datax );
		} else {
			$datax = self::$data;
		}
		return $datax;
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidgetDataProvidor::getOptionsFormat()
	 */
	public function getOptionsFormat() {
		return '{["from":"field_from"],["to":"field_to"],["placeholder":""]}';
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidgetDataProvidor::setOptions()
	 */
	public function setOptions($options) {
		$this->options = $options;
	}
}