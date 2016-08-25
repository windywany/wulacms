<?php
/**
 * 内容模型自定义字段表单.
 * @author Guangfeng
 *
 */
class ModelFieldForm extends AbstractForm {
	private $id = array ('rules' => array ('regexp(/^[0-9]+$/)' => '非法的编号.' ) );
	private $model;
	private $name = array ('rules' => array ('required' => '字段名不能为空.','regexp(/^[a-z][a-z0-9_]*$/i)' => '字段名只能是字母,数字和下划线的组合.','callback(@checkRefId,id,model)' => '字段名已经存在.' ) );
	private $label;
	private $tip;
	private $required;
	private $searchable;
	private $group = array ('rules' => array ('regexp(/^[\d]+$/i)' => '只能是数字。' ) );
	private $col = array ('rules' => array ('regexp(/^([1][0-2]?|[0-9])$/i)' => '只能是大于0小于13的数字。' ) );
	private $type = array ('rules' => array ('required' => '请选择一个输入控件.' ) );
	private $cstore;
	private $sort = array ('type' => 'int','rules' => array ('range(0,1000)' => '最小值为0，最大值999。' ) );
	private $defaults;
	private $default_value;
	private $data_type = array ('rules' => array ('regexp(/^(text|int)$/)' => '请正确填写值类型' ) );
	private $tab_acc = array ('rules' => array ('regexp(/^(tab|acc):[a-z0-9]+/)' => '只能是tab:或acc:开头且组名只能是字母数字组合.' ) );
	/**
	 * 加载自定义字段,返回自定义字段配置信息.
	 *
	 * @param AbstractForm $form        	
	 * @param string $model        	
	 * @return multitype:multitype:string
	 */
	public static function loadCustomerFields(&$form, $model) {
		$widgets = array ();
		$fields = dbselect ( 'required,name,type as widget,label,tip as note,defaults,`group`,col,default_value as `default`,tab_acc' )->from ( '{cms_model_field}' )->where ( array ('model' => $model,'deleted' => 0 ) );
		$fields->asc ( 'sort' );
		$fields = $fields->toArray ();
		$fields = apply_filter ( 'load_page_common_fields', $fields );
		foreach ( $fields as $field ) {
			if (isset ( $field ['rules'] ) && $field ['rules']) {
				$form [$field ['name']] = array ('rules' => $field ['rules'],'skip' => true );
			} else if (isset ( $field ['required'] ) && $field ['required']) {
				$form [$field ['name']] = array ('rules' => array ('required' => $field ['label'] . '必须填写.' ),'skip' => true );
			}
			if (isset ( $field ['group'] ) && empty ( $field ['group'] )) {
				unset ( $field ['group'], $field ['col'] );
			}
			$widgets [] = $field;
		}
		return $widgets;
	}
	/**
	 * 检测ID是否重复.
	 *
	 * @param string $value        	
	 * @param array $data        	
	 * @param string $message        	
	 * @return Ambigous <string, string, unknown>|boolean
	 */
	public function checkRefId($value, $data, $message) {
		$rst = dbselect ( 'id' )->from ( '{cms_model_field}' );
		$where ['name'] = $value;
		$where ['model'] = $data ['model'];
		if (! empty ( $data ['id'] )) {
			$where ['id !='] = $data ['id'];
		}
		$rst->where ( $where );
		if ($rst->count ( 'id' ) > 0) {
			return $message;
		}
		return true;
	}
	public function getRequiredValue($value) {
		if ($value) {
			return 1;
		} else {
			return 0;
		}
	}
	public function getSearchableValue($value) {
		if ($value) {
			return 1;
		} else {
			return 0;
		}
	}
}