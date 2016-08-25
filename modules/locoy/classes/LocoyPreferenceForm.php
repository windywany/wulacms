<?php
class LocoyPreferenceForm extends AbstractForm {
	private $locoy_enabled = array ('group' => '1','col' => 3,'label' => '启用火车头','widget' => 'radio','defaults' => "0=否\n1=是",'default' => '0' );
	private $locoy_secret = array ('group' => '1','col' => 3,'label' => '接入安全码','rules' => array ('required' => '请填写接入安全码' ) );
	/*
	 * (non-PHPdoc) @see AbstractForm::init_form_fields()
	 */
	protected function init_form_fields($data, $value_set) {
		$status = array ();
		$pageStatus = get_cms_page_status ();
		foreach ( $pageStatus as $id => $v ) {
			if ($id) {
				$status [] = $id . '=' . $v;
			}
		}
		$status = implode ( "\n", $status );
		$field = array ('group' => '1','col' => '3','label' => '默认状态','note' => '建议选择"待入库"','widget' => 'select','default' => '8','defaults' => $status );
		if ($value_set) {
			$field ['value'] = $data ['status'];
		}
		$this->addField ( 'status', $field );
		
		$this->addField ( '_sp', AbstractForm::seperator ( '模型选择' ) );
		
		$models = dbselect ( '*' )->from ( '{cms_model}' )->where ( array ('deleted' => 0,'hidden' => 0,'status' => 1,'creatable' => 1 ) );
		$ms = array ();
		foreach ( $models as $m ) {
			$ms [] = $m ['refid'] . '=' . $m ['name'] . "({$m['refid']})";
		}
		$models = implode ( "\n", $ms );
		$field = array ('type' => 'array','widget' => 'checkbox','defaults' => $models );
		if ($value_set) {
			$field ['value'] = $data ['allowed_models'];
		}
		$this->addField ( 'allowed_models', $field );
	}
}
