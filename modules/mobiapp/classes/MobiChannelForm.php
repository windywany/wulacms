<?php
class MobiChannelForm extends AbstractForm {
	private $id = array ('widget' => 'hidden','rules' => array ('digits' => '编号只能是数字.' ) );
	private $orefid = array ('widget' => 'hidden' );
	private $name = array ('group' => 1,'col' => 4,'label' => '栏目名称','rules' => array ('required' => '请填写栏目名称.' ) );
	private $refid = array ('group' => 1,'col' => 4,'label' => '栏目编号','rules' => array ('required' => '请填写栏目编号.','regexp(/^[1-9][\d]{0,9}$/)' => '编号只能是数字,最大长度为10','callback(@checkRefId,id)' => '编号已经存在.' ) );
	private $has_carousel = array ('type' => 'int','group' => 1,'col' => 4,'label' => '是否有轮播图','widget' => 'radio','default' => '0','defaults' => "0=否\n1=是" );
	private $hidden = array ('type' => 'int','group' => 2,'col' => 4,'label' => '默认显示','widget' => 'radio','default' => '0','defaults' => "1=否\n0=是" );
	private $sort = array ('type' => 'int','group' => 2,'col' => 4,'label' => '默认排序','default' => '999','rules' => array ('required' => '请填写默认排序','regexp(/^(0|[1-9]\d{0,2})$/)' => '排序值为0-999' ) );
	private $flags = array ('group' => 2,'col' => 4,'label' => '提示属性','note' => '以,号分隔的字符表示此栏目的属性.' );
	private $channels = array ('label' => '绑定到CMS栏目','widget' => 'checkbox' );
	
	/*
	 * (non-PHPdoc) @see AbstractForm::init_form_fields()
	 */
	protected function init_form_fields($data, $value_set) {
		$chs = dbselect ( 'refid,name' )->from ( '{cms_channel}' )->where ( array ('deleted' => 0,'hidden' => 0 ) );
		$dchs = array ();
		foreach ( $chs as $ch ) {
			$dchs [] = $ch ['refid'] . '=' . $ch ['name'];
		}
		$this->__form_fields ['channels']->setOptions ( array ('defaults' => implode ( "\n", $dchs ) ) );
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
		$rst = dbselect ( 'refid' )->from ( '{mobi_channel}' );
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
	public static function getBindChannels($refid) {
		return dbselect ( 'CH.name,CH.refid' )->from ( '{mobi_channel_binds} AS MCB' )->join ( '{mobi_channel} AS MCH', 'MCB.mobi_refid = MCH.refid' )->join ( '{cms_channel} AS CH', 'MCB.cms_refid = CH.refid' )->where ( array ('MCH.deleted' => 0,'MCB.mobi_refid' => $refid ) )->toArray ();
	}
	public static function getAllChannels($assoc = false) {
		if ($assoc) {
			return dbselect ( 'name,refid' )->from ( '{mobi_channel} AS MCB' )->where ( array ('MCB.deleted' => 0 ) )->toArray ( 'name', 'refid' );
		} else {
			return dbselect ( 'name,refid' )->from ( '{mobi_channel} AS MCB' )->where ( array ('MCB.deleted' => 0 ) )->toArray ();
		}
	}
}