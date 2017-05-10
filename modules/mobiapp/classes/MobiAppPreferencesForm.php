<?php

class MobiAppPreferencesForm extends AbstractForm {
	private $murl     = array('label' => 'H5页面根URL', 'rules' => ['url' => '请正确填写URL']);
	private $host     = array('label' => '软件包下载URL', 'note' => '如果填写，则生成url的host为该值。如:http://baidu.com');
	private $apk_home = array('label' => '生成的渠道包存放路径', 'note' => '需要有可读写权限', 'rules' => array('required' => '请填写渠道包存放路径', 'regexp(/^([\d\w_]+)(\/[\d\w_]+)*$/i)' => '路径只能包括数字，字母和下划线.', 'callback(@checkApkHome)' => '路径不可用.'));
	private $zipalign = array('label' => 'zipalign文件路径', 'note' => '需要可执行的zipalign文件路径', 'rules' => array('regexp(/.+zipalign(\.exe)?$/i)' => '文件名必须为zipalign(.exe)', 'callback(@checkZipalgin)' => '指定的zipalign不可用'));
	private $config   = array('label' => '配置说明', 'widget' => 'textarea', 'note' => '每一行为：name,default,label,note');

	public function checkApkHome($value, $data, $message) {
		$value = WEB_ROOT . $value;
		if (!file_exists($value)) {
			return $message;
		}
		if (!is_writable($value)) {
			return $message;
		}

		return true;
	}

	public function checkZipalgin($value, $data, $message) {
		if (empty ($value)) {
			return true;
		}
		if (!file_exists($value)) {
			return $message;
		}
		if (!is_executable($value)) {
			return $message;
		}

		return true;
	}
}