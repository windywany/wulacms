<?php
/**
 * 区块表单.
 * @author Guangfeng
 *
 */
class BlockForm extends AbstractForm {
	private $id = array ('rules' => array ('regexp(/^[0-9]+$/)' => '非法的编号.' ) );
	private $catelog = array ('rules' => array ('required' => '请选择分类','regexp(/^[0-9]+$/)' => '非法的分类编号.' ) );
	private $name = array ('rules' => array ('required' => '区块名不能为空.' ) );
	private $refid = array ('rules' => array ('required' => '引用名不能为空.','regexp(/^[a-z][a-z0-9_]*$/i)' => '引用名只能是字母,数字和下划线的组合.','callback(@checkRefId,id)' => '引用名已经存在.' ) );
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
		$rst = dbselect ( 'id' )->from ( '{cms_block}' );
		$where ['refid'] = $value;
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
