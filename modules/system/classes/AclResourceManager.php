<?php
/**
 * ACL 资源管理器.
 * @author Guangfeng
 *
 */
class AclResourceManager {
	private $root;
	public function __construct() {
		$this->root = new AclResource ( '/' );
	}
	/**
	 *
	 * @param string $id        	
	 * @param string $name        	
	 * @return AclResource
	 */
	public function getResource($id = '', $name = '') {
		if (empty ( $id ) || $id == '/') {
			return $this->root;
		} else {
			$ids = explode ( '/', $id );
			$node = $this->root;
			$path = array ();
			while ( ($id = array_shift ( $ids )) != null ) {
				$path [] = $id;
				$node = $node->getNode ( $id, implode ( '/', $path ) );
			}
			if (! empty ( $name )) {
				$node->setName ( $name );
			}
			return $node;
		}
	}
}