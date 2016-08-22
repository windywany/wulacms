<?php
class TreeViewWidget implements IFieldWidget, IFieldWidgetDataProvidor {
	private $options = array ();
	private $multi = false;
	private $placeholder = '';
	public function getName() {
		return '树型选择器';
	}
	public function getType() {
		return 'treeview';
	}
	public function getDataProvidor($options) {
		$this->setOptions ( $options );
		return $this;
	}
	public function render($definition, $cls = '') {
		$url = tourl ( 'system/ajax/treedata' );
		$id = $definition ['id'];
		$name = $definition ['name'];
		if (! $id) {
			$id = $name;
		}
		$ops = $this->getDataProvidor ( $definition ['defaults'] )->getData ();
		
		if ($ops ['table']) {
			$text = TreeViewWidget::getTreeValueText ( $ops ['table'], $definition ['value'], $ops ['idf'], $ops ['namef'] );
			if (isset ( $ops ['placeholder'] ) && ! isset ( $definition ['placeholder'] )) {
				$definition ['placeholder'] = $ops ['placeholder'];
			}
			$url .= '?' . http_build_query ( $ops );
			$readonly = isset ( $definition ['readonly'] ) ? ' readonly="readonly" ' : '';
			$disabled = isset ( $definition ['disabled'] ) ? ' disabled="disabled" ' : '';
			$placeholder = isset ( $definition ['placeholder'] ) ? ' placeholder="' . $definition ['placeholder'] . '"' : '';
			return '<input type="hidden"
							data-widget="nuiTreeview"
							style="width:100%"
							data-source="' . $url . '"
							data-multi="' . $this->multi . '"
							data-text="' . $text . '"
							name="' . $name . '" id="' . $id . '" value="' . $definition ['value'] . '"' . $readonly . $disabled . $placeholder . '/>';
		} else {
			return '';
		}
	}
	public function getOptionsFormat() {
		return '{"table":"表名","idf":"ID字段","namef":"文本字段","upidf":"上级ID字段","pid":"父级ID值","cid":"当前ID","params":{"param1":"v1"},"multi":[true|false]}';
	}
	public function setOptions($options) {
		$this->options = @json_decode ( $options, true );
		if (! $this->options) {
			$this->options = array();
		}
		$this->multi = isset ( $this->options ['multi'] ) ? $this->options ['multi'] : false;
		$this->placeholder = isset ( $this->options ['placeholder'] ) ? $this->options ['placeholder'] : '';
		unset ( $this->options ['multi'], $this->options ['placeholder'] );
		$this->options = array_merge ( array ('idf' => 'id','namef' => 'name','upidf' => 'upid' ), $this->options );
	}
	public function getData($option = false) {
		if (! $option) {
			return $this->options;
		}
		return array ();
	}
	/**
	 * 取树形数据表示字符串.
	 *
	 * @param string $table
	 *        	表名.
	 * @param string $ids
	 *        	ID列表.
	 * @param string $idf
	 *        	id 字段名.
	 * @param string $namef
	 *        	text 字段名.
	 * @return 提示字符.
	 */
	public static function getTreeValueText($table, $ids, $idf = 'id', $namef = 'name') {
		$names = '';
		$ids = safe_ids2 ( $ids );
		if ($ids) {
			$names = dbselect ( $namef )->from ( '{' . $table . '}' )->where ( array ($idf . ' IN' => $ids ) )->toArray ( $namef );
		}
		return $names ? implode ( ',', $names ) : '';
	}
}