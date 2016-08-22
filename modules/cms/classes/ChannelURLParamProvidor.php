<?php

namespace cms\classes;

class ChannelURLParamProvidor extends \URLParamProvidor {
	private $where = array ();
	public function where($where) {
		$this->where = $where;
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see URLParamProvidor::getValue()
	 */
	public function getURLValue($value = null) {
		if (is_null ( $value )) {
			$rvalue = $this->value;
		} else {
			$rvalue = $value;
		}
		if ($rvalue) {
			return preg_replace ( '#/index\.(s?html?)#', '', $rvalue );
		} else {
			return '';
		}
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see URLParamProvidor::getData()
	 */
	protected function getData() {
		$where = $this->where;
		if ($this->parentValue) {
			$w = array ('url' => $this->parentValue,'deleted' => 0,'hidden' => 0 );
			$parent = dbselect ()->from ( '{cms_channel}' )->where ( $w )->get ( 'id' );
			if ($parent) {
				$where ['upid'] = $parent;
			} else {
				return array ();
			}
		}
		$where ['deleted'] = 0;
		$where ['hidden'] = 0;
		$channels = dbselect ( 'url,name' )->from ( '{cms_channel}' )->where ( $where )->asc ( 'sort' )->toArray ( 'name', 'url', array ($this->emptyVal => $this->emptyText ) );
		return $channels;
	}
}

?>