<?php

namespace system\classes;

class CatalogURLParamProvidor extends \URLParamProvidor {
	private $where = array ();
	public function where($where) {
		$this->where = array_merge ( $this->where, $where );
	}
	public function type($type) {
		$this->where ['type'] = $type;
	}
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see URLParamProvidor::getData()
	 */
	protected function getData() {
		if (! isset ( $this->where ['type'] ) || empty ( $this->where ['type'] )) {
			return array ();
		}
		$where = $this->where;
		$where ['upid'] = 0;
		if ($this->parentValue) {
			$where ['upid'] = $this->parentValue;
		}
		$where ['deleted'] = 0;
		$catalogs = dbselect ( 'id,name' )->from ( '{catalog}' )->where ( $where )->toArray ( 'name', 'id', array ($this->emptyVal => $this->emptyText ) );
		return $catalogs;
	}
}

?>