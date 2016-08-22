<?php
/**
 * 遍历树形结构数据。
 * @author Guangfeng
 *
 */
class TreeIterator {
	private $nodes = array ();
	public function __construct($data, $from = 0, $keyfield = 'id', $upfield = 'upid') {
		$node = new KissCmsTreeNode ( $from, null, $data, $this, $keyfield, $upfield, array () );
	}
	public function addNode($id, $node) {
		$this->nodes [$id] = $node;
	}
	/**
	 * 取解析后的结点.
	 *
	 * @param int $id
	 * @return KissCmsTreeNode
	 */
	public function getNode($id) {
		return isset ( $this->nodes [$id] ) ? $this->nodes [$id] : null;
	}
	public function hasNode($id) {
		return isset ( $this->nodes [$id] );
	}
	public function getNodes() {
		return $this->nodes;
	}
	public function getNodeSubIds($sep = null) {
		$nodes = array ();
		foreach ( $this->nodes as $id => $node ) {
			$nodes [$id] = $sep == null ? $node->getSubIds () : implode ( $sep, $node->getSubIds () );
		}
		return $nodes;
	}
	
	/**
	 * 更新关系树
	 *
	 * @author DQ
	 *         @date 2016年3月14日 下午6:08:26
	 * @param
	 *        	string 表名 {tableName}
	 * @param
	 *        	array 条件
	 * @param
	 *        	string 主键
	 * @param
	 *        	string 存储父类字段名
	 * @param
	 *        	string 存储子类字段名
	 * @return
	 *
	 *
	 */
	public static function updateTreeNode($table = '', $where = array(), $primaryCol = '', $relationCol = '', $parentCol = '', $subCol = '', $dialect = 'default') {
		if (empty ( $table ) || empty ( $where ) || empty ( $primaryCol ) || empty ( $relationCol ) || empty ( $parentCol ) || empty ( $subCol )) {
			return false;
		}
		// 计算子级和父级分类
		$dialect = DatabaseDialect::getDialect ( $dialect );
		// 取所有子级数据
		$tree = dbselect ( $primaryCol . ',' . $relationCol )->setDialect ( $dialect )->from ( $table )->where ( $where )->toArray ();
		// 遍历树形数据
		$iterator = new TreeIterator ( $tree, 0, $primaryCol, $relationCol );
		$nodes = $iterator->getNodes ();
		unset ( $nodes [0] );
		foreach ( $nodes as $id => $node ) {
			$parents = $node->getParentsIdList ( $primaryCol );
			if ($parents) {
				$parents = implode ( ',', $parents );
			} else {
				$parents = '';
			}
			dbupdate ( $table )->setDialect ( $dialect )->set ( array ($parentCol => $parents,$subCol => implode ( ',', $node->getSubIds () ) ) )->where ( array ($primaryCol => $id ) )->exec ();
		}
	}
}
/**
 * 树结点.
 *
 * @author Guangfeng
 *
 */
class KissCmsTreeNode {
	private $parent;
	private $nexts = array ();
	private $ids = array ();
	private $id;
	private $data;
	/**
	 *
	 * @param int $id
	 * @param KissCmsTreeNode $parent
	 * @param array $data
	 * @param TreeIterator $iterator
	 */
	public function __construct($id, $parent, $data, $iterator, $keyfield, $upfield, $ndata) {
		if (! $iterator->hasNode ( $id )) {
			$this->parent = $parent;
			$this->id = $id;
			$this->data = $ndata;
			$iterator->addNode ( $id, $this );
			foreach ( $data as $n ) {
				if ($n [$upfield] == $id) {
					$this->nexts [$n [$keyfield]] = new KissCmsTreeNode ( $n [$keyfield], $this, $data, $iterator, $keyfield, $upfield, $n );
				}
			}
			$this->addNextId ( $id );
		}
	}
	public function addNextId($id) {
		array_unshift ( $this->ids, $id );
		if ($this->parent) {
			$this->parent->addNextId ( $id );
		}
	}
	public function getId() {
		return $this->id;
	}
	/**
	 *
	 * @return KissCmsTreeNode 父节点.
	 */
	public function getParent() {
		return $this->parent;
	}
	public function getParents(&$parents) {
		if ($this->parent) {
			$pid = $this->parent->getId ();
			$parents [$pid] = $this->parent;
			$this->parent->getParents ( $parents );
		}
	}
	public function getParentsIdList($field, $glue = null) {
		$parents = array ();
		$this->getParents ( $parents );
		$list = array ();
		foreach ( $parents as $node ) {
			$data = $node->getData ();
			if ($data) {
				if (isset ( $data [$field] )) {
					$list [] = $data [$field];
				} else {
					$list [] = '';
				}
			}
		}
		if (empty ( $glue )) {
			return $list;
		} else {
			return implode ( $glue, $list );
		}
	}
	public function getData() {
		return $this->data;
	}
	public function getChildren() {
		return $this->nexts;
	}
	public function getSubIds() {
		return $this->ids;
	}
}
?>