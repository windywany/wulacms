<?php

class PassportPreferenceForm extends AbstractForm {
	private $allow_remote     = array('group' => '0', 'col' => '3', 'label' => '做为通行证服务器', 'widget' => 'radio', 'default' => '0', 'defaults' => "0=否\n1=是");
	private $redirect_url     = array('group' => '0', 'col' => '9', 'label' => '登录成功后默认跳转到页面', 'rules' => array('url' => '请填写正确的URL。'));
	private $_sp1             = array('widget' => 'htmltag', 'defaults' => '<section class="timeline-seperator"><span>登录设置</span></section>');
	private $enable_oauth     = array('group' => '1_0', 'col' => '3', 'label' => '启用第三方登录', 'widget' => 'radio', 'default' => '0', 'defaults' => "0=否\n1=是");
	private $enable_captcha1  = array('group' => '1_0', 'col' => '3', 'label' => '启用登录验证码', 'widget' => 'radio', 'default' => '0', 'defaults' => "0=否\n1=是");
	private $force_bind_phone = array('group' => '1_0', 'col' => '3', 'label' => '第三方登录强制绑定会员', 'widget' => 'radio', 'default' => '0', 'defaults' => "0=否\n1=是");

	private $_sp             = array('widget' => 'htmltag', 'defaults' => '<section class="timeline-seperator"><span>注册设置</span></section>');
	private $allow_join      = array('group' => '1', 'col' => '3', 'label' => '允许注册', 'widget' => 'radio', 'note' => '允许邮件与手机注册', 'default' => '0', 'defaults' => "0=否\n1=是");
	private $join_type       = array('group' => '1', 'col' => '3', 'label' => '默认注册方式', 'widget' => 'radio', 'note' => '邮件或手机', 'default' => 'mail', 'defaults' => "mail=邮件\nphone=手机");
	private $enable_captcha  = array('group' => '1', 'col' => '3', 'label' => '启用注册验证码', 'widget' => 'radio', 'default' => '1', 'defaults' => "0=否\n1=是");
	private $mail_active     = array('group' => '1', 'col' => '3', 'label' => '邮件注册必须激活', 'widget' => 'radio', 'default' => '1', 'defaults' => "0=否\n1=是");
	private $enable_invation = array('group' => '4', 'col' => '3', 'label' => '启用邀请机制', 'widget' => 'radio', 'default' => '0', 'defaults' => "0=否\n1=是");
	private $invite_required = array('group' => '4', 'col' => '3', 'label' => '必须邀请', 'widget' => 'radio', 'default' => '0', 'defaults' => "0=否\n1=是", 'note' => '启用邀请机制后生效');
	private $avatar_dir      = array('group' => '4', 'col' => '3', 'label' => '头像目录', 'note' => '默认为avatar', 'rules' => array('regexp(/^[a-z][\da-z_]*$/i)' => '只能是字母,数字,下划线。'));
	private $default_group   = array('group' => '2', 'col' => '3', 'label' => '默认用户组', 'widget' => 'auto', 'defaults' => 'user_group,group_id,group_name,pst:system/preference,,passport', 'note' => '用户注册时将用户添加到该组。');
	private $default_role    = array('group' => '2', 'col' => '3', 'label' => '默认角色', 'widget' => 'auto', 'defaults' => 'user_role,role_id,role_name,pst:system/preference,,passport', 'note' => '用户注册后用户拥有该角色。');
	private $join_url        = array('group' => '2', 'col' => '6', 'label' => '注册成功后默认跳转到页面', 'rules' => array('url' => '请填写正确的URL。'));
	private $expire          = array('group' => '3', 'col' => '3', 'label' => '会员组赠送时长', 'note' => '格式：1m表示一个月，1d表示一天', 'default' => '0d', 'rules' => array('regexp(/^(0|[1-9]\d*)(m|d)$/)' => '请正确填写时长(以m或d结尾)'));
	private $agree           = array('label' => '服务条款', 'widget' => 'textarea', 'note' => '允许HTML代码.');

	public function formatDefault_roleValue($value) {
		if ($value) {
			$name = dbselect()->from('{user_role}')->where(array('role_id' => $value))->get('role_name');
			if ($name) {
				return $value . ':' . $name;
			}
		}

		return $value;
	}

	public function formatDefault_groupValue($value) {
		if ($value) {
			$name = dbselect()->from('{user_group}')->where(array('group_id' => $value))->get('group_name');
			if ($name) {
				return $value . ':' . $name;
			}
		}

		return $value;
	}
}