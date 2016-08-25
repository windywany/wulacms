<?php
/**
 * 内容模型表单.
 * @author Guangfeng
 *
 */
class ModelForm extends AbstractForm {
	private $upid = array ('rules' => array ('regexp(/^[0-9]+$/)' => '非法的上级内容模型编号.' ) );
	private $id = array ('rules' => array ('regexp(/^[0-9]+$/)' => '非法的内容模型编号.' ) );
	private $name = array ('rules' => array ('required' => '内容模型名不能为空.' ) );
	private $refid = array ('rules' => array ('required' => '识别ID不能为空.','regexp(/^[a-z][a-z0-9_]*$/i)' => '识别ID只能是字母,数字和下划线的组合.','callback(@checkRefId,id)' => '识别ID已经存在.' ) );
	private $status;
	private $is_topic_model;
	private $is_list_model = array ('type' => 'bool' );
	private $creatable = array ('type' => 'bool' );
	private $addon_table = array ('rules' => array ('regexp(/^[a-z][a-z0-9_]+$/i)' => '附加数据表名称不合法.' ) );
	private $search_page_prefix = array ('rules' => array ('regexp(/^[a-z0-9\_\/]+$/i)' => '搜索页面前缀只能是字母和数字的组合.','callback(@checkPrefix,id)' => '搜索页面前缀已经存在.' ) );
	private $search_page_tpl = array ('rules' => array ('required(search_page_prefix:filled)' => '请填写模板文件','regexp(/\.tpl$/)' => '模板文件必须以tpl结尾' ) );
	private $search_page_limit = array ('rules' => array ('required(search_page_prefix:filled)' => '请填写每页显示的搜索结果条数','regexp(/^(0|[1-9][\d]*)$/)' => '每页显示的搜索结果条数只能是数字.' ) );
	private $template = array ('rules' => array ('regexp(/\.tpl$/)' => '模板文件必须以tpl结尾.' ) );
	private $role;
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
		$rst = dbselect ( 'id' )->from ( '{cms_model}' );
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
	public function checkPrefix($value, $data, $message) {
		if (empty ( $value )) {
			return true;
		}
		$rst = dbselect ( 'id' )->from ( '{cms_model}' );
		$where ['search_page_prefix'] = $value;
		if (! empty ( $data ['id'] )) {
			$where ['id !='] = $data ['id'];
		}
		$rst->where ( $where );
		if ($rst->count ( 'id' ) > 0) {
			return $message;
		}
		return true;
	}
	public function getIs_topic_modelValue($value) {
		if ($value) {
			return 1;
		} else {
			return 0;
		}
	}
}