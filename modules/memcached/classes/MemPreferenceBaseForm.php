<?php
class MemPreferenceBaseForm {
	public static function init($form) {
		$form ['enabled'] = array (
				'label' => '是否启用缓存',
				'widget' => 'radio',
				'defaults' => "1=启用\n0=不启用",
				'default' => '0' 
		);
		$form ['compress_enabled'] = array (
				'label' => '是否启用压缩',
				'widget' => 'radio',
				'defaults' => "1=启用\n0=不启用",
				'default' => '0' 
		);
		$form ['cache_expire'] = array (
				'group' => '1',
				'col' =>'4',
				'label' => '默认缓存时间',
				'note' => '单位为秒',
				'default' => '7200',
				'rules' => array (
						'required' => '必须填写.',
						'digits' => '请填写正确的缓存时间' 
				) 
		);
		$form ['index_expire'] = array (
				'group' => '1',
				'col' =>'4',
				'label' => '首页缓存时间',
				'note' => '单位为秒',
				'default' => '3600',
				'rules' => array (						
						'digits' => '请填写正确的缓存时间'
				)
		);
		$form ['resource_expire'] = array (
				'group' => '1',
				'col' =>'4',
				'label' => '合并资源(js,css)缓存时间',
				'note' => '单位为秒',
				'default' => '31536000',
				'rules' => array (
						'digits' => '请填写正确的缓存时间'
				)
		);
		$form ['servers'] = array (
				'label' => '缓存服务器',
				'widget' => 'textarea',
				'rules' => array (
						'required' => '必须填写，每行一个服务器(ip:port)' 
				),
				'note' => '缓存服务器的地址(IP:PORT),每行一个服务器。' 
		);
	}
}

?>