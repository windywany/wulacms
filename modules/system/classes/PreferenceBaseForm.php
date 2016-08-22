<?php
class PreferenceBaseForm {
	/**
	 *
	 * @param DynamicForm $form
	 */
	public static function init($form) {
		$defaultApps = apply_filter ( 'get_default_apps', array ('' => '系统默认' ) );
		$dapps = array ();
		foreach ( $defaultApps as $id => $name ) {
			$dapps [] = $id . '=' . $name;
		}
		$defaultApps = implode ( "\n", $dapps );
		$timezones [] = 'Etc/GMT+12=西12区';
		$timezones [] = 'Etc/GMT+11=西11区';
		$timezones [] = 'Etc/GMT+10=西10区';
		$timezones [] = 'Etc/GMT+9=西9区';
		$timezones [] = 'Etc/GMT+8=西8区';
		$timezones [] = 'Etc/GMT+7=西7区';
		$timezones [] = 'Etc/GMT+6=西6区';
		$timezones [] = 'Etc/GMT+5=西5区';
		$timezones [] = 'Etc/GMT+4=西4区';
		$timezones [] = 'Etc/GMT+3=西3区';
		$timezones [] = 'Etc/GMT+2=西2区';
		$timezones [] = 'Etc/GMT+1=西1区';
		$timezones [] = 'Etc/GMT+0=UTC';
		$timezones [] = 'Etc/GMT-1=东1区';
		$timezones [] = 'Etc/GMT-2=东2区';
		$timezones [] = 'Etc/GMT-3=东3区';
		$timezones [] = 'Etc/GMT-4=东4区';
		$timezones [] = 'Etc/GMT-5=东5区';
		$timezones [] = 'Etc/GMT-6=东6区';
		$timezones [] = 'Etc/GMT-7=东7区';
		$timezones [] = 'Etc/GMT-8=东8区';
		$timezones [] = 'Etc/GMT-9=东9区';
		$timezones [] = 'Etc/GMT-10=东10区';
		$timezones [] = 'Etc/GMT-11=东11区';
		$timezones [] = 'Etc/GMT-12=东12区';
		
		$form ['debug_level'] = array ('group' => '1','col' => '3','label' => '调试级别','widget' => 'select','default' => '4','defaults' => "2=调试\n3=警告\n4=信息\n5=错误\n6=关闭" );
		$form ['timezone'] = array ('group' => '1','col' => '3','label' => '时区','widget' => 'select','default' => 'Etc/GMT-8','defaults' => implode ( "\n", $timezones ) );
		$form ['default_app'] = array ('group' => '1','col' => '3','label' => '默认应用','widget' => 'select','defaults' => $defaultApps,'default' => '' );
		$form ['session_expire'] = array ('group' => '1','col' => '3','label' => '操作超时','note' => '多久不操作后台,系统将退出,单位为秒.不填写则永不超时.','default' => '900','rules' => array ('regexp(/^(0|[1-9][0-9]+)$/)' => '只能是数字.' ) );
		
		$hd = opendir ( THEME_PATH . THEME_DIR );
		$themes = array ();
		if ($hd) {
			while ( ($f = readdir ( $hd )) != false ) {
				if ($f != '.' && $f != '..' && is_dir ( THEME_PATH . THEME_DIR . DS . $f )) {
					$themes [] = $f . '=' . $f;
				}
			}
			closedir ( $hd );
		}
		$form ['theme'] = array ('group' => '1_5','col' => '3','label' => '模板主题','widget' => 'select','defaults' => implode ( "\n", $themes ),'default' => 'default' );
		$form ['mobi_domain'] = array ('group' => '1_5','col' => '6','label' => '移动版域名','rules' => array ('regexp(/^[a-z0-9][a-z\d_\.]*[a-z0-9]*$/i)' => '移动版域名,不包括http://' ) );
		$form ['mobi_theme'] = array ('group' => '1_5','col' => '3','label' => '移动版模板主题','widget' => 'select','defaults' => implode ( "\n", $themes ),'default' => 'default' );
		$form ['isOffline'] = array ('label' => '系统维护','widget' => 'radio','default' => '0','defaults' => "0=否\n1=是" );
		$form ['allowIps'] = array('group' => '1_6','col' => '3','label'=>'允许IP','note'=>'一行一个','widget'=>'textarea','rows'=>8);
		$form ['offlineMsg'] = array('group' => '1_6','col' => '9','label'=>'系统维护提示信息','widget'=>'textarea','rows'=>8);
		$form ['reserved_domains'] = array ('label' => '保留的二级域名.' );
		$form ['site_name'] = array ('group' => '2','col' => '4','label' => '网站名称','rules' => array ('required' => '请填写网站名称。' ) );
		$form ['site_url'] = array ('group' => '2','col' => '8','label' => '网站URL','note' => '不填写则使用系统自动检测到的URL.','rules' => array ('url' => '请填写正确的URL。' ) );
		$form ['site_beian'] = array ('group' => '3','col' => '4','label' => '网站备案号' );
		$form ['site_copyright'] = array ('group' => '3','col' => '8','label' => '版权信息' );
		$form ['site_title'] = array ('label' => '网站首页标题' );
		$form ['keywords'] = array ('label' => '默认SEO关键词','widget' => 'textarea' );
		$form ['description'] = array ('label' => '默认SEO描述','widget' => 'textarea' );
		$form ['develop_mode'] = array ('group' => '5','col' => '3','label' => '开发模式','widget' => 'radio','default' => '0','defaults' => "1=开启\n0=关闭" );
		$form ['combinate'] = array ('group' => '5','col' => '3','label' => '合并资源','widget' => 'radio','default' => '0','defaults' => "1=开启\n0=关闭" );
		$form ['enable_remote_data'] = array ('group' => '6','col' => '3','label' => '启用远程数据源','note' => '仅用于本地套模板时使用.','widget' => 'radio','default' => '0','defaults' => "1=启用\n0=不启用" );
		$form ['remote_data_url'] = array ('group' => '6','col' => '9','label' => '远程地址','note' => '需要与数据源接入同一RestFULL服务器.','rules' => array ('required(enable_remote_data_1:checked:1)' => '请填写远程数据源地址.','url' => '请填写正确的URL.' ) );
		$form ['langs'] = array ('label' => '多语言支持','widget' => 'textarea','note' => '一行一个语言和模板设置,格式为(id;label[;template]). 如zh_cn;中文;default' );
	}
}