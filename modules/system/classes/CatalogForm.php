<?php
/**
 * 分类表单.
 * @author Guangfeng
 *
 */
class CatalogForm extends AbstractForm {
	private $upid = array ('rules' => array ('regexp(/^[0-9]+$/)' => '非法的父类编号.' ) );
	private $id = array ('rules' => array ('regexp(/^[0-9]+$/)' => '非法的常量编号.' ) );
	private $name = array ('rules' => array ('required' => '不能为空.' ) );
	private $note;
	private $type = array ('rules' => array ('required' => '类型不能为空','callback(@checkType)' => '未知的枚举类型.' ) );
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
		$rst = dbselect ( 'id' )->from ( '{catalog}' );
		$where ['alias'] = $value;
		$where ['type'] = $data ['type'];
		$where ['upid'] = $data ['upid'];
		if (! empty ( $data ['id'] )) {
			$where ['id !='] = $data ['id'];
		}
		$rst->where ( $where );
		if ($rst->count ( 'id' ) > 0) {
			return $message;
		}
		return true;
	}
	public function checkType($value, $data, $message) {
		$catalogTypes = apply_filter ( 'get_catalog_types', array () );
		if (isset ( $catalogTypes [$value] )) {
			return true;
		}
		return $message;
	}
	public static function on_init_rest_server($server) {
		return $server->registerClass ( new CatalogForm (), '1', 'catalog' );
	}
	public function rest_getCatalogTree($params, $key = '', $secret = '') {
		if (isset ( $params ['type'] ) && ! empty ( $params ['type'] )) {
			$type = $params ['type'];
			$types = explode ( ',', $type );
			$trees = array ();
			foreach ( $types as $type ) {
				$tree = dbselect ()->from ( '{catalog}' )->treeWhere ( array ('type' => $type,'deleted' => 0 ) );
				$options = array ();
				$tree->treeOption ( $options );
				$trees [$type] = $options;
			}
			return $trees;
		} else {
			return array ('error' => 1,'message' => 'no type specified!' );
		}
	}
	/**
	 *
	 * @param array $params
	 *        	('type'=>'type')
	 * @param string $key
	 * @param string $secret
	 */
	public function rest_getCatalogOptions($params, $key = '', $secret = '') {
		if (isset ( $params ['type'] ) && ! empty ( $params ['type'] )) {
			$type = $params ['type'];
			
			$options = isset ( $params ['options'] ) ? $params ['options'] : array ();
			
			$options = dbselect ( 'name,id' )->from ( '{catalog}' )->where ( array ('type' => $type,'deleted' => 0 ) )->toArray ( 'name', 'id', $options );
			
			return $options;
		} else {
			return array ('error' => 1,'message' => 'no type specified!' );
		}
	}
	/**
	 * 添加或更新item,成功返回item id,失败返回0.
	 *
	 * @param string $type
	 *        	类型.
	 * @param string $item
	 *        	item,可以以$sp分隔，表示多级.
	 * @param string $sp
	 *        	分隔符.
	 * @return integer item id or 0 on failure.
	 */
	public static function updateItem($type, $item, $sp = '/') {
		$id = 0;
		$its = explode ( $sp, trim ( $item ) );
		foreach ( $its as $d ) {
			$d = trim ( $d );
			if ($d) {
				$id = self::checkItem ( $d, $type, $id );
				if ($id == 0) {
					break;
				}
			}
		}
		TreeIterator::updateTreeNode ( '{catalog}', array ('type' => $type,'deleted' => 0 ), 'id', 'upid', 'parents', 'sub' );
		return $id;
	}
	public static function cataname($ids,$c=',',$limit=null){
		$ids = safe_ids2($ids);
		if($ids){
			$catalog = dbselect ( 'name' )->from ( '{catalog}' )->where ( array ('id IN' => $ids ) );
			if(is_numeric($limit)){
				$catalog->limit(0, $limit);
			}
			$catalog = $catalog->toArray('name');
			if($catalog){
				return implode($c, $catalog);
			}
		}
		return '';
	}
	// 检测条目是否存在.
	private static function checkItem($item, $type, $upid) {
		$data = array ('type' => $type,'name' => $item,'upid' => $upid );
		$id = dbselect ()->from ( '{catalog}' )->where ( $data )->get ( 'id' );
		if ($id) {
			return $id;
		}
		$data ['upid'] = $upid;
		$data ['alias'] = Pinyin::c ( $item );
		$data ['deleted'] = 0;
		$data ['create_time'] = $data ['update_time'] = time ();
		$data ['update_uid'] = $data ['create_uid'] = 1;
		$id = dbinsert ( $data )->into ( '{catalog}' )->exec ();
		if ($id && $id [0]) {
			$id = $id [0];
			return $id;
		}
		return 0;
	}
}