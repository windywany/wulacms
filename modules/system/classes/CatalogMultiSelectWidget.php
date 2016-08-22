<?php
class CatalogMultiSelectWidget implements IFieldWidget, IFieldWidgetDataProvidor {
	private $options;
	public function getDataProvidor($options) {
		$this->setOptions ( $options );
		return $this;
	}
	public function getType() {
		return 'mcatalog';
	}
	public function getName() {
		return false;
	}
	public function render($definition, $cls = '') {
		$defaults = explode ( ',', $definition ['defaults'] );
		$catalog = isset ( $defaults [0] ) ? $defaults [0] : false;
		if (empty ( $catalog )) {
			return '';
		}
		$value = isset ( $definition ['value'] ) ? $definition ['value'] : '';
		$data = $this->getValues ( $value );
		$id = isset ( $definition ['id'] ) ? $definition ['id'] : $definition ['name'];
		$text = isset ( $definition ['text'] ) ? $definition ['text'] : '选择';
		$title = empty ( $definition ['title'] ) ? '请选择' : $definition ['title'];
		$width = empty ( $definition ['width'] ) ? '450' : $definition ['width'];
		$ss = isset ( $defaults [1] ) && $defaults [1];
		$url = tourl ( 'system/catalog/ms/' . $catalog ) . $ss;
		$html [] = '<label class="input input-file" for="' . $id . '"><div class="button" target="dialog" dialog-model="true" data-for="#' . $id . '"';
		$html [] = ' dialog-title="' . $title . '"';
		$html [] = ' dialog-width="' . $width . '"';
		$html [] = ' data-url="' . $url . '"';
		$html [] = '>';
		$html [] = $text . '</div>';
		$html [] = '<input type="text" readonly="readonly"/>';
		$html [] = '</label>';
		
		$html [] = '<div class="well well-sm item-removable" data-widget="nuiTagWrapper">';
		$html [] = '<input type="hidden" id="' . $id . '" name="' . $definition ['name'] . '" value="' . $value . '"/><div class="tags-wrapper">';
		if ($data) {
			foreach ( $data as $d ) {
				$html [] = '<span class="badge removable">' . $d ['name'] . '<a class="close" data-value="' . $d ['id'] . '">&times;</a></span>';
			}
		}
		$html [] = '</div><b class="clearfix"/>';
		$html [] = '</div>';
		return implode ( '', $html );
	}
	public function getData($option = false) {
		return array ();
	}
	public function setOptions($options) {
		$this->options = $options;
	}
	public function getOptionsFormat() {
		return '参数格式为：type[,<0|1>]。type 为常量标识.多选时指定1.';
	}
	private function getValues($values) {
		$ids = safe_ids ( $values, ',', true );
		$data = false;
		if ($ids) {
			$data = dbselect ( '*' )->from ( '{catalog}' )->where ( array ('id IN' => $ids ) )->toArray ();
		}
		return $data;
	}
}