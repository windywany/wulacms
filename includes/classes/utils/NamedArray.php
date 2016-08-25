<?php
class NamedArray implements ArrayAccess, Countable, IteratorAggregate {
	protected $attrs = array ();
	protected $attr_prefix = '';
	public function __construct($ary = array()) {
		if (is_array ( $ary )) {
			$this->attrs = $ary;
		}
	}
	/**
	 * 设置属性的前缀.
	 *
	 * @param string $prefix        	
	 * @return NamedArray
	 */
	public function prefix($prefix = '') {
		$this->attr_prefix = $prefix;
		return $this;
	}
	public function __set($attr, $value) {
		$attr = str_replace ( '_', '-', $attr );
		if ($this->attr_prefix) {
			$attr = $this->attr_prefix . $attr;
		}
		$this->attrs [$attr] = $value;
	}
	public function __get($attr) {
		$attr = str_replace ( '_', '-', $attr );
		if (isset ( $this->attrs [$attr] )) {
			return $this->attrs [$attr];
		}
		return '';
	}
	public function __call($name, $args) {
		if ($args) {
			$this->__set ( $name, $args [0] );
			return $this;
		} else {
			return $this->__get ( $name );
		}
	}
	public function get($name, $default = '') {
		return isset ( $this->attrs [$name] ) ? $this->attrs [$name] : $default;
	}
	public function &ref($name) {
		if (isset ( $this->attrs )) {
			return $this->attrs [$name];
		} else {
			return null;
		}
	}
	public function combine_array($obj) {
		if ($obj instanceof NamedArray) {
			$obj = $obj->toArray ();
		}
		if (is_array ( $obj ) && $obj) {
			$this->attrs = array_merge_recursive ( $this->attrs, $obj );
		}
	}
	
	/*
	 * (non-PHPdoc) @see ArrayAccess::offsetExists()
	 */
	public function offsetExists($offset) {
		return isset ( $this->attrs [$offset] );
	}
	
	/*
	 * (non-PHPdoc) @see ArrayAccess::offsetGet()
	 */
	public function offsetGet($offset) {
		return $this->attrs [$offset];
	}
	
	/*
	 * (non-PHPdoc) @see ArrayAccess::offsetSet()
	 */
	public function offsetSet($offset, $value) {
		$this->attrs [$offset] = $value;
	}
	
	/*
	 * (non-PHPdoc) @see ArrayAccess::offsetUnset()
	 */
	public function offsetUnset($offset) {
		unset ( $this->attrs [$offset] );
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see Countable::count()
	 */
	public function count($mode = null) {
		return count ( $this->attrs );
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see IteratorAggregate::getIterator()
	 */
	public function getIterator() {
		return new ArrayIterator ( $this->attrs );
	}
	public function toArray() {
		return $this->attrs;
	}
}