<?php
class CatalogSelectWidget implements IFieldWidget, IFieldWidgetDataProvidor {
	private $options;
	public static function get_custom_field_widgets($widgets) {
		$widgets->register ( new CatalogSelectWidget () );
		$widgets->register ( new CatalogRadioWidget () );
		$widgets->register ( new CatalogCheckboxWidget () );
		$widgets->register ( new CatalogMultiSelectWidget () );
		$widgets->register ( new DatepickerFieldWidget () );
		$widgets->register ( new TimepickerFieldWidget () );
	}
	public function getDataProvidor($options) {
		$this->setOptions ( $options );
		return $this;
	}
	public function getType() {
		return 'catalog';
	}
	public function getName() {
		return '数据选择控件';
	}
	public function render($definition, $cls = '') {
		$defaults = $definition ['defaults'];
		$data = $this->getDataProvidor ( $defaults )->getData ();
		$id = isset ( $definition ['id'] ) ? $definition ['id'] : $definition ['name'];
		$readonly = isset ( $definition ['readonly'] ) ? ' readonly="readonly" ' : '';
		$disabled = isset ( $definition ['disabled'] ) ? ' disabled="disabled" ' : '';
		$html [] = '<label class="select"><select id="' . $id . '" name="' . $definition ['name'] . '"' . $disabled . $readonly . '>';
		$html [] = '<option value="">请选择</option>';
		if ($data) {
			foreach ( $data as $key => $d ) {
				if ($key == $definition ['value']) {
					$html [] = '<option value="' . $key . '" selected="selected">' . $d . '</option>';
				} else {
					$html [] = '<option value="' . $key . '">' . $d . '</option>';
				}
			}
		}
		$html [] = '</select><i></i></label>';
		return implode ( '', $html );
	}
	public function getData($option = false) {
		if (empty ( $this->options )) {
			return array ();
		}
		$args = explode ( ',', $this->options );
		$type = $args [0];
		$tree = false;
		$upid = 0;
		if (isset ( $args [1] )) {
			$tree = ! empty ( $args [1] );
		}
		$where = array ('type' => $type,'deleted' => 0 );
		if (isset ( $args [2] ) && is_numeric ( $args [2] )) {
			$upid = $args [2];
		}
		if ($tree) {
			$tree = dbselect ()->from ( '{catalog}' )->treeWhere ( $where );
			$options = array ();
			if ($option) {
				$tree->treePad ( false );
			}
			if ($upid) {
				$tree->treeOption ( $options, 'id', 'upid', 'name', null, $upid );
			} else {
				$tree->treeOption ( $options );
			}
			return $options;
		} else {
			if ($upid) {
				$where ['upid'] = $upid;
			}
			return dbselect ( 'id,name' )->from ( '{catalog}' )->where ( $where )->toArray ( 'name', 'id' );
		}
	}
	public function setOptions($options) {
		$this->options = $options;
	}
	public function getOptionsFormat() {
		return '参数格式为：type[,<0|1>[,upid]]。type 为常量标识.需要加载树形数据时指定参数1.';
	}
	/**
	 * 取变量值的id
	 *
	 * @param string $type        	
	 * @param string $alias        	
	 * @return Ambigous <Ambigous, NULL, unknown, multitype:>
	 */
	public static function getItemId($type, $alias) {
		return dbselect ()->from ( '{catalog}' )->where ( array ('type' => $type,'deleted' => 0,'alias' => $alias ) )->get ( 'id' );
	}
	/**
	 * 取变量值的别名
	 *
	 * @param int $id        	
	 * @return Ambigous <Ambigous, NULL, unknown, multitype:>
	 */
	public static function getItemAlias($id) {
		return dbselect ()->from ( '{catalog}' )->where ( array ('id' => $id,'deleted' => 0 ) )->get ( 'alias' );
	}
}
?>