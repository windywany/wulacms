<?php
class UpYunPreferencesForm extends AbstractForm {
    private $working = array ('label' => '是否开启','widget'=>'radio','default' => '0','defaults' => "1=启用\n0=不启用");
	private $bucketname = array ('label' => '空间名','rules' => array ('required' => '请填写空间名'));
	private $username = array ('label' => '用户名','rules' => array ('required' => '请填写用户名' ) );
	private $password = array ('label' => '密码', 'rules' => array ('required' => '请填写密码' ));
	
	public function checkApkHome($value, $data, $message) {
		$value = WEB_ROOT . $value;
		if (! file_exists ( $value )) {
			return $message;
		}
		if (! is_writable ( $value )) {
			return $message;
		}
		return true;
	}
	public function checkZipalgin($value, $data, $message) {
		if (empty ( $value )) {
			return true;
		}
		if (! file_exists ( $value )) {
			return $message;
		}
		if (! is_executable ( $value )) {
			return $message;
		}
		return true;
	}
}