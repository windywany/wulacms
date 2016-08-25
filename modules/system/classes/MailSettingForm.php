<?php
/**
 * mail setting form.
 * @author ngf
 *
 */
class MailSettingForm extends AbstractForm {
	private $smtp_type = array ('label' => '邮件发送方式','widget' => 'radio','default' => '','defaults' => "smtp=SMTP\nsendmail=SendMail\n=关闭" );
	private $smtp_secure = array ('label' => '启用安全发送','widget' => 'radio','default' => '','defaults' => "=不启用\ntls=TLS\nssl=SSL" );
	private $smtp_host = array ('group' => 1,'col' => '6','label' => '服务器地址','note' => '域名或IP.','rules' => array ('required(smtp_type_smtp:checked:smtp)' => '请填写服务器主机地址.' ) );
	private $smtp_port = array ('group' => 1,'col' => '6','label' => '服务器端口','rules' => array ('required(smtp_type_smtp:checked:smtp)' => '请填写服务器端口.','digits' => '端口只能是数字','callback(@checkport)' => '端口不合法' ) );
	private $smtp_auth = array ('label' => '登录验证','widget' => 'radio','default' => '0','defaults' => "0=否\n1=是" );
	private $smtp_user = array ('group' => 2,'col' => '6','label' => '用户名','rules' => array ('required(smtp_auth_1:checked:1)' => '请填写用户名.' ) );
	private $smtp_passwd = array ('group' => 2,'col' => '6','label' => '密码','widget' => 'password','rules' => array ('required(smtp_user:filled)' => '请填写密码.' ) );
	private $smtp_from = array ('group' => 3,'col' => '6','label' => '发件人姓名' );
	private $smtp_reply = array ('group' => 3,'col' => '6','label' => '回复到' );
	public function checkport($value, $data, $msg) {
		$value = intval ( $value );
		if ($value > 0 && $value < 65535) {
			return true;
		}
		return $msg;
	}
}
