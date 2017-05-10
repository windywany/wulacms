<?php
class AppVersionForm extends AbstractForm {
	private $id = array ('widget' => 'hidden','rules' => array ('digits' => '编号只能是数字.','regexp(/^[1-9][\d]*$/)' => '编号只能是数字,最大长度为10' ) );
	private $app_id = array ('group' => 1,'col' => 4,'label' => '应用名称','widget' => 'auto','defaults' => 'rest_apps,id,name,pst:system/preference','rules' => array ('required' => '请选择名称.' ) );
	private $version = array ('group' => 1,'col' => 4,'label' => '版本名称','rules' => array ('required' => '请填写版本名称.' ) );
	private $vername = array ('group' => 1,'col' => 4,'label' => '版本号','rules' => array ('required' => '请添加版本号.','callback(@checkVersion,id)' => '版本已经存在' ),'note' => '判断应用是否更新，仅能为数字' );
	private $os = array ('group' => 2,'col' => 4,'label' => '操作系统','widget' => 'radio','rules' => array ('required' => '请选择操作系统.' ),'default' => '1','defaults' => "1=Android\n2=iOS" );
	private $update_type = array ('group' => 2,'col' => 4,'label' => '是否强制更新','widget' => 'radio','rules' => array ('required' => '请选择更新类型.' ),'default' => '0','defaults' => "0=否\n1=是\n2=不更新" );
	private $prefix = array ('group' => 3,'col' => 4,'label' => '文件名前缀','note' => 'IOS应用时此值必须为','rules' => array ('regexp(/^[a-z0-9_]*$/i)' => '文件名前缀只能是字母,数字和下划线的组合.' ) );
	private $apk_file = array ('group' => 3,'col' => 8,'label' => '应用母包文件','widget' => 'image','defaults' => '{"extensions":"apk,ipa","water":0,"locale":1,"msize":"1000M"}','rules' => array ('regexp(/.+(apk|ipa)$/i)' => '只允许apk,ipa文件','callback(@checkApkFile)' => '文件不存在' ) );
	private $desc = array ('label' => '更新说明','widget' => 'textarea' );
	private $attr = array ('widget' => 'hidden' );
	private $url = array ('label' => '下载地址','note'=>'安卓包会自动生成，IOS可以填写市场地址.' );
	
	/*
	 * (non-PHPdoc) @see AbstractForm::init_form_fields()
	 */
	protected function init_form_fields($data, $value_set) {
		$cfgs = cfg ( 'config@mobiapp' );
		if ($cfgs) {
			$cfgs = explode ( "\n", $cfgs );
			$save = json_decode ( $data ['attr'], true );
			foreach ( $cfgs as $cfg ) {
				$cfg = trim ( $cfg );
				$cfgx = explode ( ',', $cfg );
				if ($save) {
					$this->addField ( $cfgx [0], array ('skip' => true,'label' => $cfgx [2],'default' => $save [$cfgx [0]],'note' => $cfgx [3] ) );
				} else {
					$this->addField ( $cfgx [0], array ('skip' => true,'label' => $cfgx [2],'default' => $cfgx [1],'note' => $cfgx [3] ) );
				}
			}
		}
	}
	public function getAttrValue($value) {
		$json = array ();
		$cfgs = cfg ( 'config@mobiapp' );
		if ($cfgs) {
			$cfgs = explode ( "\n", $cfgs );
			foreach ( $cfgs as $cfg ) {
				$cfg = trim ( $cfg );
				$cfgx = explode ( ',', $cfg );
				if (empty ( $cfgx [0] )) {
					continue;
				}
				$json [$cfgx [0]] = rqst ( $cfgx [0] );
			}
			return json_encode ( $json );
		} else {
			return "";
		}
	}
	public function checkApkFile($value, $data, $message) {
		if ($value && ! file_exists ( WEB_ROOT . $value )) {
			return $message;
		}
		return true;
	}
	public function checkVersion($value, $data, $message) {
		$where ['vername'] = $value;
		if ($data ['id']) {
			$where ['id <>'] = $data ['id'];
		}
		$db = dbselect ();
		$exist = $db->from ( '{app_version}' )->where ( $where )->exist ( 'id' );
		if ($exist) {
			return $message;
		}
		return true;
	}
}
