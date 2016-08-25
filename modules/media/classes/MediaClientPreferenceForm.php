<?php
class MediaClientPreferenceForm extends AbstractForm {
	private $store_type = array (
			'label' => '接入多媒体服务器',
			'widget' => 'radio',
			'default' => '0',
			'defaults' => "0=否\n1=是"
	);	
	
	private $media_url = array (
			'label' => '多媒体服务器URL',
			'default' => '',
			'note'=>'多个URL以英文逗号分隔',
			'rules' => array (
					'required(store_type_1:checked:1)' => '请填写多媒体服务器URL',
					'url' => '请填写正确的URL。'
			)
	);
	private $url = array (
			'label' => '多媒体服务器接口URL',
			'default' => '',
			'rules' => array (
					'required(store_type_1:checked:1)' => '请填写多媒体服务器接口URL',
					'url' => '请填写正确的URL。'
			)
	);
	private $appkey = array (
			'label' => '应用ID',
			'rules' => array (
					'required(store_type_1:checked:1)' => '请填写应用ID'
			)
	);
	private $appsecret = array (
			'label' => '应用安全码',
			'rules' => array (
					'required(store_type_1:checked:1)' => '请填写应用ID'
			)
	);
}

?>