<?php
class PassportPreferenceForm extends AbstractForm {
	private $type = array ('group' => '0','col' => '3','label' => '通行证类型','widget' => 'radio','default' => 'vip','defaults' => "admin=管理员\nvip=会员" );
	private $allow_remote = array ('group' => '0','col' => '3','label' => '做为通行证服务器','widget' => 'radio','default' => '0','defaults' => "0=否\n1=是" );
	private $enable_auth = array ('group' => '0','col' => '3','label' => '允许实名认证','widget' => 'radio','default' => '0','defaults' => "0=否\n1=是" );
	private $layout = array ('group' => '0_0','col' => '3','label' => '主题','widget' => 'select' );
	private $style = array ('group' => '0_0','col' => '3','label' => '样式','widget' => 'combox' );
	private $redirect_url = array ('group' => '0_0','col' => '6','label' => '登录成功后跳转到页面','rules' => array ('url' => '请填写正确的URL。' ) );
	private $_sp0 = array ('widget' => 'htmltag','defaults' => '<section class="timeline-seperator"><span>接入设置</span></section>' );
	private $connect_to = array ('group' => '0_1','col' => '3','label' => '接入通行证服务器','widget' => 'radio','default' => '0','defaults' => "0=否\n1=是" );
	private $sync_member = array ('group' => '0_1','col' => '3','label' => '同步会员数据','widget' => 'radio','default' => '0','defaults' => "0=否\n1=是" );
	private $passport_url = array ('group' => '0_2','col' => '6','label' => '接入通行证的登录页面','rules' => array ('required(connect_to_1:checked:1)' => '请填写','url' => '请填写正确的URL。' ) );
	private $passport_rest_url = array ('group' => '0_2','col' => '6','label' => '接入通行证的RESTFull地址','note' => '通行证所在服务器的RESTFll服务地址.','rules' => array ('required(connect_to_1:checked:1)' => '请填写','url' => '请填写正确的URL。' ) );
	private $_sp = array ('widget' => 'htmltag','defaults' => '<section class="timeline-seperator"><span>注册设置</span></section>' );
	private $allow_join = array ('group' => '1','col' => '3','label' => '允许注册','widget' => 'radio','default' => '0','defaults' => "0=否\n1=是" );
	private $enable_phone = array ('group' => '1','col' => '3','label' => '开启手机注册','widget' => 'radio','default' => '0','defaults' => "0=否\n1=是" );
	private $enable_captcha = array ('group' => '1','col' => '3','label' => '启用验证码','widget' => 'radio','default' => '1','defaults' => "0=否\n1=是" );
	private $enable_oauth = array ('group' => '1','col' => '3','label' => '启用第三方登录','widget' => 'radio','default' => '0','defaults' => "0=否\n1=是" );
	private $enable_active = array ('group' => '4','col' => '3','label' => '启用激活机制','widget' => 'radio','default' => '0','defaults' => "0=否\n1=是" );
	private $code_expire = array ('group' => '4','col' => '3','label' => '激活码有效时间','widget' => 'text','default' => '24','note' => '单位为小时' );
	private $enable_invation = array ('group' => '4','col' => '3','label' => '启用邀请机制','widget' => 'radio','default' => '0','defaults' => "0=否\n1=是" );
	private $invite_required = array ('group' => '4','col' => '3','label' => '必须邀请','widget' => 'radio','default' => '0','defaults' => "0=否\n1=是",'note'=>'启用邀请机制后生效' );
	private $default_group = array ('group' => '2','col' => '3','label' => '默认用户组','widget' => 'auto','defaults' => 'user_group,group_id,group_name,pst:system/preference,,passport','note' => '用户注册时将用户添加到该组。' );
	private $default_role = array ('group' => '2','col' => '3','label' => '默认角色','widget' => 'auto','defaults' => 'user_role,role_id,role_name,pst:system/preference,,passport','note' => '用户注册后用户拥有该角色。' );
	private $join_url = array ('group' => '2','col' => '6','label' => '注册成功后默认跳转到页面','rules' => array ('url' => '请填写正确的URL。' ) );
	private $agree = array ('label' => '服务条款','widget' => 'textarea','note' => '允许HTML代码.' );
	public function init_form_fields($data, $value_set) {
		$layouts = apply_filter ( 'get_passport_theme', array ('UCHomeTheme=默认主题' ) );
		$this->__form_fields ['layout']->setOptions ( array ('defaults' => implode ( "\n", $layouts ),'default' => 'UCHomeTheme' ) );
		$url = tourl ( 'passport/preference/styles' );
		$this->__form_fields ['style']->setOptions ( array ('defaults' => '{"parent":"layout","url":"' . $url . '"}' ) );
	}
	public function formatDefault_roleValue($value) {
		if ($value) {
			$name = dbselect ()->from ( '{user_role}' )->where ( array ('role_id' => $value ) )->get ( 'role_name' );
			if ($name) {
				return $value . ':' . $name;
			}
		}
		return $value;
	}
	public function formatDefault_groupValue($value) {
		if ($value) {
			$name = dbselect ()->from ( '{user_group}' )->where ( array ('group_id' => $value ) )->get ( 'group_name' );
			if ($name) {
				return $value . ':' . $name;
			}
		}
		return $value;
	}
	public function formatRedirect_appidValue($value) {
		if ($value) {
			$appname = dbselect ()->from ( '{rest_apps}' )->where ( array ('appkey' => $value ) )->get ( 'name' );
			if ($appname) {
				return $value . ':' . $appname;
			}
		}
		return $value;
	}
	public function formatStyleValue($value) {
		$layout = cfg ( 'layout@passport', 'UCHomeTheme' );
		$styles = self::getStyles ( $layout );
		$style = cfg ( 'style@passport' );
		if ($style) {
			$style = $style . ':' . $styles [$style];
		}
		return $style;
	}
	public static function getStyles($layout) {
		if (! $layout || ! class_exists ( $layout )) {
			return array ();
		}
		$layoutClz = new $layout ();
		if ($layoutClz instanceof IPassportTheme) {
			return $layoutClz->getStyles ();
		}
		return array ();
	}
}