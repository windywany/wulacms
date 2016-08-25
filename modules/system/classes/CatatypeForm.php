<?php
class CatatypeForm extends AbstractForm {
	private $id = array ('widget' => 'hidden','id' => 'catatype_id' );
	private $name = array ('label' => '数据名','group' => 1,'col' => 4,'rules' => array ('required' => '请填写类型名' ) );
	private $type = array ('label' => '数据标识','group' => 1,'col' => 3,'rules' => array ('required' => '请填写类型标识','regexp(/^[a-z][a-z\d_]*$/i)' => '只能是字母，数字，下划线的组合','callback(@checkType,id)' => '类型标识已经存在。' ) );
	private $is_enum = array ('label' => '数据类型','group' => 1,'col' => 5,'type' => 'bool','widget' => 'radio','default' => '0','defaults' => "0=树结构\n1=列表" );
	private $note = array ('label' => '备注','widget' => 'textarea' );
	public function checkType($value, $data, $message) {
		$rst = dbselect ( 'id' )->from ( '{catalog_type}' );
		$where ['type'] = $value;
		if (! empty ( $data ['id'] )) {
			$where ['id !='] = $data ['id'];
		}
		$rst->where ( $where );
		if ($rst->count ( 'id' ) > 0) {
			return $message;
		}
		return true;
	}
	/**
	 * hook for 'get_catalog_types'
	 *
	 * @param unknown $types        	
	 * @return Ambigous <multitype:, string, unknown>
	 */
	public static function get_catalog_types($types, $all = true) {
		if ($all) {
			$catelogTypes = dbselect ( '*' )->from ( '{catalog_type}' )->where ( array ('deleted' => 0 ) )->toArray ( null, 'type' );
			if ($catelogTypes) {
				foreach ( $catelogTypes as $key => $v ) {
					$types [$key] = $v;
				}
			}
		}
		return $types;
	}
	public static function get_model_link_groups($groups) {
		$groups ['catalog'] = '枚举与分类';
		return $groups;
	}
}