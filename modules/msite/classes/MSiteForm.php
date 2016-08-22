<?php
class MSiteForm extends AbstractForm {
	private $id = array ('rules' => array ('regexp(/^[0-9]+$/)' => '非法的编号.' ) );
	private $domain = array ('rules' => array ('required' => '请填写域名.','regexp(/^[a-z0-9][a-z0-9]*$/i)' => '域名只能是字母数字下划线组成.','callback(@checkDomain,id)' => '域名已经存在.' ) );
	private $mdomain = array ('rules' => array ('regexp(/^[a-z0-9][a-z0-9]*$/i)' => '域名只能是字母数字下划线组成.','callback(@checkDomain,id)' => '域名已经存在.' ) );
	private $channel = array ('rules' => array ('required' => '请选择要绑定的栏目.' ) );
	private $topics;
	private $theme;
	public function checkDomain($value, $data, $message) {
		$rst = dbselect ( 'id' )->from ( '{cms_msite}' );
		$where ['domain'] = $value;
		if (! empty ( $data ['id'] )) {
			$where ['id !='] = $data ['id'];
		}
		$rst->where ( $where );
		if ($rst->count ( 'id' ) > 0) {
			return $message;
		}
		$domains = cfg ( 'reserved_domains' );
		if ($domains) {
			$domains = explode ( ',', $domains );
			if (in_array ( $value, $domains )) {
				return $message;
			}
		}
		return true;
	}
}
