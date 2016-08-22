<?php
/**
 * 角色表单.
 * @author Guangfeng
 *
 */
class UserRoleForm extends AbstractForm {
	private $role_id = array ('rules' => array ('regexp(/^[0-9]+$/)' => '非法的角色编号.' ) );
	private $priority = array ('rules' => array ('required' => '请填写角色的权重','regexp(/^[0-9]{0,3}$/)' => '权重只能是三位数字.' ) );
	private $role_name = array ('rules' => array ('required' => '角色名不能为空.' ) );
	private $role = array ('rules' => array ('required' => 'ID不能为空.','callback(@checkRefId,role_id)' => 'ID已经存在.' ) );
	private $type = array ('rules' => array ('required' => '请选择角色类型','callback(@checkType)' => '类型不存在.' ) );
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
		$rst = dbselect ( 'role_id' )->from ( '{user_role}' );
		$where ['role'] = $value;
		if (! empty ( $data ['role_id'] )) {
			$where ['role_id !='] = $data ['role_id'];
		}
		$rst->where ( $where );
		if ($rst->count ( 'role_id' ) > 0) {
			return $message;
		}
		return true;
	}
	public function checkType($value, $data, $message) {
		$types = UserGroupForm::getGroupTypes ();
		return isset ( $types [$value] ) ? true : $message;
	}
}