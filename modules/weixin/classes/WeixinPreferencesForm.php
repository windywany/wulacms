<?php
class WeixinPreferencesForm extends AbstractForm {
	private $Type = array ('label' => '主账号','widget' => 'select','group' => 1,'col' => 2,'defaults' => "1=公众号\n2=订阅号\n3=第三方" );
	private $Username = array ('label' => '微信号','group' => 1,'col' => 2,'rules' => array ('required' => '请填写微信号' ) );
	private $AppID = array ('label' => 'AppID','group' => 1,'col' => 4,'rules' => array ('required' => '请填写AppID' ) );
	private $AppSecret = array ('label' => 'AppSecret','group' => 1,'col' => 4,'rules' => array ('required' => '请填写AppSecret' ) );
	private $Token = array ('label' => 'Token','group' => 2,'col' => 4,'note' => '必须为英文或数字，长度为3-32字符。','rules' => array ('required' => '请填写Token','regexp(/^[\da-z]{3,32}$/i)' => '格式错误' ) );
	private $EncodingAESKey = array ('label' => 'EncodingAESKey','group' => 2,'col' => 8,'note' => '43位随机字符，可通过微信后台生成.','rules' => array ('required(EncodingType_2:checked)' => '请填写EncodingAESKey','regexp(/^[\da-z]{43}$/i)' => '格式错误' ) );
	private $EncodingType = array ('label' => '通信加密方式','group' => 3,'col' => 4,'widget' => 'radio','defaults' => "0=明文模式\n1=兼容模式\n2=加密模式" );
	
	private $QRImage = array ('label' => '微信二维码','group' => 3,'col' => 8,'widget' => 'image','defaults'=>'{"water":0}');
	
	private $_bbb = array ('skip' => true,'widget' => 'htmltag','defaults' => '<section class="timeline-seperator"><span>登录账号</span></section>' );
	private $IsSame = array ('label' => '同主账号','group' => 5,'col' => 4,'widget' => 'radio','default' => 1,'defaults' => "0=否\n1=是",'note' => '主账号是订阅号时请选择否' );
	private $GetUserInfo = array ('label' => '获取用户信息','group' => 5,'col' => 4,'widget' => 'radio','default' => 1,'defaults' => "0=否\n1=是" );
	private $UpdateInfo = array ('label' => '用户信息更新周期','group' => 5,'col' => 4,'widget' => 'select','default' => 1,'defaults' => "-1=永不更新\n0=每次登录\n1=每天\n7=每周\n30=每月\n180=每半年\n365=每年" );
	private $LoginUsername = array ('label' => '登录微信号','group' => 6,'col' => 2,'rules' => array ('required(IsSame_0:checked)' => '请填写微信号' ) );
	private $LoginToken = array ('label' => 'Token','group' => 6,'col' => 2,'rules' => array ('required(IsSame_0:checked)' => '请填写Token','regexp(/^[\da-z]{3,32}$/i)' => '格式错误' ) );
	private $LoginAppID = array ('label' => '登录AppID','group' => 6,'col' => 4,'rules' => array ('required(IsSame_0:checked)' => '请填写AppID' ) );
	private $LoginAppSecret = array ('label' => '登录AppSecret','group' => 6,'col' => 4,'rules' => array ('required(IsSame_0:checked)' => '请填写AppSecret' ) );
}
