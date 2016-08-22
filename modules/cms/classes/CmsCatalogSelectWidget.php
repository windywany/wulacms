<?php
class CmsCatalogSelectWidget implements IFieldWidget, IFieldWidgetDataProvidor {
	private $options;	
	public function getDataProvidor($options) {
		$this->setOptions ( $options );
		return $this;
	}
	public function getType() {
		return 'cms_catalog';
	}
	public function getName() {
		return '内容分类选择器';
	}
	public function render($definition, $cls = '') {
		$defaults = $definition ['defaults'];
		$data = $this->getDataProvidor ( $defaults )->getData ();
		$id = isset ( $definition ['id'] ) ? $definition ['id'] : $definition ['name'];
		$html [] = '<label class="select"><select id="' . $id . '" name="' . $definition ['name'] . '">';
		
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
			$tree = dbselect ()->from ( '{cms_catelog}' )->treeWhere ( $where )->treeKey('alias');
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
			return dbselect ( 'alias,name' )->from ( '{cms_catelog}' )->where ( $where )->toArray ( 'name', 'alias' );
		}
	}
	public function setOptions($options) {
		$this->options = $options;
	}
	public function getOptionsFormat() {
		return '参数格式为：type[,<0|1>[,upid]]。type 为分类的类型.需要加载树形数据时指定参数1.';
	}
}
?>