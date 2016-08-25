<?php
/**
 * 应用中心设置.
 * @author Guangfeng
 *
 */
class RestPreferenceForm extends AbstractForm {
	private $allow_remote = array ('group' => 1,'col' => 3,'label' => '允许第三方接入','widget' => 'radio','default' => '0','defaults' => "0=否\n1=是" );
	private $session_name = array ('group' => 1,'col' => 3,'label' => '会话参数名','rules' => array ('regexp(/^[a-z][a-z0-9_]{2,15}$/)' => '字母加数字或下划线.' ) );
	private $connect_server = array ('group' => 2,'col' => 3,'label' => '接入远程应用中心','widget' => 'radio','default' => '0','defaults' => "0=否\n1=是" );
	private $url = array ('group' => 2,'col' => 9,'label' => '远程应用中心地址','note' => '接入远程应用中心启用后，将地址为远程应用中心地址.','default' => '','rules' => array ('required(connect_server_1:checked:1)' => '请填写通行证服务器接口URL','url' => '请填写正确的URL。' ) );
	private $_aaa = array('group' => 3,'col' => 3,'skip'=>true,'widget'=>'htmltag','defaults'=>'&nbsp;');
	private $appkey = array ('group' => 3,'col' => 3,'label' => '应用ID','rules' => array ('required(connect_server_1:checked:1)' => '请填写应用ID' ) );
	private $appsecret = array ('group' => 3,'col' => 6,'label' => '应用安全码','rules' => array ('required(connect_server_1:checked:1)' => '请填写应用安全码' ) );
}