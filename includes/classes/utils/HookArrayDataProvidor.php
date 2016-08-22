<?php
class HookArrayDataProvidor extends KsDataProvidor {
	public function getDataType() {
		return KsDataProvidor::ARY;
	}
	public function getData() {
		$options = $this->options;
		if (isset ( $options ['hook'] ) && $options ['hook']) {
			return apply_filter ( $options ['hook'], array () );
		}
		return array ();
	}
	public function getName() {
		return '插件数组数据源';
	}
	public function getConfigFields(&$fields) {
		$fields ['_sp_hook_array_data_providor'] = AbstractForm::seperator ( '数据源设置' );
		$fields ['hook'] = array ('label' => 'hook名称' );
	}
}
