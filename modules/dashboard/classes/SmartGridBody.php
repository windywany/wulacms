<?php
/**
 * 表格正文.
 * @author ngf
 *
 */
class SmartGridBody implements Renderable {
	private $data;
	private $fields;
	private $total = '';
	private $name = '';
	private $fieldId = '';
	private $renders = array ();
	private $checkboxEnabled = false;
	public function __construct($fields, $data, $total = '', $name = '', $fieldId = '', $sm = false) {
		$this->fields = $fields;
		$this->data = $data;
		$this->total = $total;
		$this->name = $name;
		$this->fieldId = $fieldId;
		$this->checkboxEnabled = $sm;
		foreach ( $this->fields as $f ) {
			if ($f) {
				$render = array ($this,'render_' . $f );
				if (is_callable ( $render )) {
					$this->renders [$f] = $render;
				}
			}
		}
	}
	public function render() {
		$html [] = '<tbody data-total="' . $this->total . '">';
		$i = 0;
		foreach ( $this->data as $data ) {
			$total = count ( $this->data );
			$html [] = '<tr';
			if ($this->name && $this->fieldId && isset ( $data [$this->fieldId] )) {
				$html [] = ' name="' . $this->name . '" rel="' . $data [$this->fieldId] . '"';
			}
			$html [] = '>';
			if ($this->checkboxEnabled) {
				$html [] = '<td>';
				if ($this->name && $this->fieldId && isset ( $data [$this->fieldId] )) {
					$html [] = '<input type="checkbox" name="' . $this->name . '" value="' . $data [$this->fieldId] . '" class="grp"/>';
				} else {
					$html [] = '<input type="checkbox" class="grp"/>';
				}
				$html [] = '</td>';
			}
			foreach ( $this->fields as $f ) {
				if (isset ( $this->renders [$f] )) {
					$html [] = call_user_func_array ( $this->renders [$f], array ($data,$this->data,$i,$total ) );
				} else {
					$html [] = '<td>';
					if ($f) {
						if (isset ( $data [$f] )) {
							$html [] = $data [$f];
						}
					}
					$html [] = '</td>';
				}
			}
			$html [] = '</tr>';
			$i ++;
		}
		$html [] = '</tbody>';
		return implode ( '', $html );
	}
}