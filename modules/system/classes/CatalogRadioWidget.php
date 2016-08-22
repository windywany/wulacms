<?php
class CatalogRadioWidget implements IFieldWidget, IFieldWidgetDataProvidor {
	private $options;
	public function getDataProvidor($options) {
		$this->setOptions ( $options );
		return $this;
	}
	public function getType() {
		return 'catalogradio';
	}
	public function getName() {
		return '数据单选控件';
	}
	public function render($definition, $cls = '') {
		$defaults = $definition ['defaults'];
		$data = $this->getDataProvidor ( $defaults )->getData ();
		$html [] = '<div class="inline-group">';
		$id = isset ( $definition ['id'] ) ? $definition ['id'] : $definition ['name'];
		if ($data) {
			$readonly = isset ( $definition ['readonly'] ) ? ' readonly="readonly" ' : '';
			$disabled = isset ( $definition ['disabled'] ) ? ' disabled="disabled" ' : '';
			foreach ( $data as $key => $d ) {
				$checked = '';
				if ($d) {
					$val = ' value="' . $key . '" ';
					if ($key == $definition ['value']) {
						$checked = ' checked="checked" ';
					}
				} else {
					if ($definition ['value']) {
						$checked = ' checked="checked" ';
					}
					$d = $key;
					$val = '';
				}
				$html [] = '<label class="radio"><input id="' . $id . '_' . $key . '" type="radio" ' . $checked . $readonly . $disabled . ' name="' . $definition ['name'] . '"' . $val . '/><i></i>' . $d . '</label>';
			}
		}
		$html [] = '</div>';
		return implode ( '', $html );
	}
	public function getData($option = false) {
		if (empty ( $this->options )) {
			return array ();
		}
		$args = explode ( ',', $this->options );
		$type = $args [0];
		$where = array ('type' => $type,'deleted' => 0 );
		return dbselect ( 'id,name' )->from ( '{catalog}' )->where ( $where )->toArray ( 'name', 'id' );
	}
	public function setOptions($options) {
		$this->options = $options;
	}
	public function getOptionsFormat() {
		return '参数格式为：type。type 为数据标识.';
	}
}