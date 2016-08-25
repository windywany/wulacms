<?php
/**
 * 用户组表单.
 * @author Guangfeng
 *
 */
class UserGroupForm extends AbstractForm {
	private $upid = array ('rules' => array ('regexp(/^[0-9]+$/)' => '非法的用户组编号.' ) );
	private $group_id = array ('rules' => array ('regexp(/^[0-9]+$/)' => '非法的用户组编号.' ) );
	private $group_name = array ('rules' => array ('required' => '用户组名不能为空.' ) );
	private $group_refid = array ('rules' => array ('required' => 'ID不能为空.','callback(@checkRefId,group_id)' => 'ID已经存在.' ) );
	private $type = array ('rules' => array ('required' => '请选择账户组类型.','callback(@checkType)' => '类型不存在.' ) );
	private $note;
	/**
	 * 检测ID是否重复.
	 *
	 * @param string $value
	 * @param array $data
	 * @param string $message
	 * @return Ambigous <string, string, unknown>|boolean
	 */
	public function checkRefId($value, $data, $message) {
		$rst = dbselect ( 'group_id' )->from ( '{user_group}' );
		$where ['group_refid'] = $value;
		if (! empty ( $data ['group_id'] )) {
			$where ['group_id !='] = $data ['group_id'];
		}
		$rst->where ( $where );
		if ($rst->count ( 'group_id' ) > 0) {
			return $message;
		}
		return true;
	}
	public function checkType($value,$data,$message){
		$types = self::getGroupTypes();
		return isset($types[$value])?true:$message;
	}
	public static function getGroupTypes() {
		$types = apply_filter ( 'get_user_group_types', array ('admin' => '管理员' ) );
		return $types;
	}
}