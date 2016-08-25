<?php
/**
 * 分类表单.
 * @author Guangfeng
 *
 */
class CatelogForm extends AbstractForm {
	private $upid = array ('rules' => array ('regexp(/^[0-9]+$/)' => '非法的父类编号.' ) );
	private $id = array ('rules' => array ('regexp(/^[0-9]+$/)' => '非法的分类编号.' ) );
	private $name = array ('rules' => array ('required' => '分类名不能为空.' ) );
	private $note;
	private $type;
	private $alias = array ('rules' => array ('required' => '请填写别名','callback(@checkAlias,type,id)' => '别名已经存在.' ) );
	/**
	 * 检测ALIAS是否重复.
	 *
	 * @param string $value        	
	 * @param array $data        	
	 * @param string $message        	
	 * @return Ambigous <string, string, unknown>|boolean
	 */
	public function checkAlias($value, $data, $message) {
		$rst = dbselect ( 'id' )->from ( '{cms_catelog}' );
		$where ['alias'] = $value;
		$where ['type'] = $data ['type'];
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