<?php
class MobiPageViewForm extends AbstractForm {
	private $id = array ('widget' => 'hidden','rules' => array ('digits' => '编号只能是数字.' ) );
	private $name = array ('group' => 1,'col' => 4,'label' => '模板名称','rules' => array ('required' => '请填写模板名称.' ) );
	private $refid = array ('group' => 1,'col' => 4,'label' => '模板编号','rules' => array ('required' => '请填写模板编号.','regexp(/^[1-9][\d]{0,9}$/)' => '编号只能是数字,最大长度为10','callback(@checkRefId,id)' => '编号已经存在.' ) );
	private $tpl = array ('group' => 1,'col' => 4,'label' => '模板文件','widget' => 'tpl','rules' => array ('required' => '请填写模板文件','regexp(/^[a-z0-9][a-z0-9_\/\-]*\.tpl$/i)' => '模板文件名格式不正确.' ) );
	private $models = array ('label' => '可以显示的模型','widget' => 'checkbox' );
	private $desc = array ('label' => '说明','widget' => 'textarea' );
	
	/*
	 * (non-PHPdoc) @see AbstractForm::init_form_fields()
	 */
	protected function init_form_fields($data, $value_set) {
		$chs = dbselect ( 'refid,name' )->from ( '{cms_model}' )->where ( array ('deleted' => 0,'hidden' => 0 ) );
		$dchs = array ();
		foreach ( $chs as $ch ) {
			$dchs [] = $ch ['refid'] . '=' . $ch ['name'];
		}
		$this->__form_fields ['models']->setOptions ( array ('defaults' => implode ( "\n", $dchs ) ) );
	}
	/**
	 * 检测ID是否重复.
	 *
	 * @param string $value        	
	 * @param array $data        	
	 * @param string $message        	
	 * @return Ambigous <string, string, unknown>|boolean
	 */
	public function checkRefId($value, $data, $message) {
		$rst = dbselect ( 'refid' )->from ( '{mobi_page_view}' );
		$where ['refid'] = $value;
		if (! empty ( $data ['id'] )) {
			$where ['id !='] = $data ['id'];
		}
		$rst->where ( $where );
		if ($rst->count ( 'refid' ) > 0) {
			return $message;
		}
		return true;
	}
}
