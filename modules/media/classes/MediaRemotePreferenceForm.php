<?php
class MediaRemotePreferenceForm extends AbstractForm {
	private $enable_download = array (
			'label' => '开启远程下载',
			'widget' => 'radio',
			'default' => '0',
			'defaults' => "0=否\n1=是"
	);
	
	private $timeout = array (
			'label' => '下载超时(单位秒)',
			'default' => '30',
			'rules' => array (					
					'digits' => '只能是数字。'
			)
	);
	private $exclude_url = array (
			'label' => '不下载以下网站的图片',
			'default' => '',
			'widget'=>'textarea',
			'note' => '每行一个域名'
	);	
}
