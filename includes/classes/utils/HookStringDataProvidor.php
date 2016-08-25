<?php
class HookStringDataProvidor extends KsDataProvidor {
	public function getDataType() {
		return KsDataProvidor::HTML;
	}
	public function getData() {
		$options = $this->options;
		if (isset ( $options ['hook'] ) && $options ['hook']) {
			return apply_filter ( $options ['hook'], '' );
		}
		return '';
	}
	public function getName() {
		return '插件文本数据源';
	}
	public function getConfigFields(&$fields) {
		$fields ['_sp_hook_string_data_providor'] = AbstractForm::seperator ( '数据源设置' );
		$fields ['hook'] = array ('label' => 'hook名称' );
	}
}