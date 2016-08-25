<?php
class RestAppForm extends AbstractForm {
	private $id = array ('rules' => array ('digits' => '非法的应用编号' ) );
	private $name = array ('rules' => array ('required' => '请填写应用名.' ) );
	private $note;
	private $appkey = array ('rules' => array ('required' => '请填写应用程序ID.','callback(@checkAppkey,id)' => '应用程序ID已经存在.' ) );
	private $appsecret = array ('rules' => array ('required' => '请填写应用安全码.' ) );
	private $callback_url = array ('rules' => array ('url' => '请填写正确的回调URL.' ) );
	/**
	 * 检测应用名是否重复.
	 *
	 * @param string $value        	
	 * @param array $data        	
	 * @param string $message        	
	 * @return Ambigous <string, string, unknown>|boolean
	 */
	public function checkAppkey($value, $data, $message) {
		$rst = dbselect ( 'id' )->from ( '{rest_apps}' );
		$where ['appkey'] = $value;
		if (! empty ( $data ['id'] )) {
			$where ['id !='] = $data ['id'];
		}
		$rst->where ( $where );
		if ($rst->count ( 'id' ) > 0) {
			return $message;
		}
		return true;
	}
}