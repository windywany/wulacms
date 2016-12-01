<?php

namespace args;

class Arg implements \IteratorAggregate, \ArrayAccess {
	protected $values       = [];
	protected $name         = '';
	protected $parent       = null;
	protected $value        = null;
	private   $valueInited  = false;
	protected $index        = '';
	protected $items        = [];
	protected $defaultValue = '0';
	/**
	 * @var ArgGroup
	 */
	protected $group = null;

	public function __construct($name = '', Arg $parent = null) {
		$this->parent = $parent;
		$this->name   = $name;
	}

	/**
	 * 初始化参数.
	 *
	 * @param array $values 参数值
	 *                      'default_value' => 'label'
	 *                      'value1'=>'label1'
	 */
	public function initWithValues($values) {
		$this->values = $values;
		$this->init();
	}

	public function isAvaliable() {
		return count($this->values);
	}

	protected function initValues() {
	}

	public function getDefaultValue() {
		return $this->defaultValue;
	}

	/**
	 * @param \args\ArgGroup $group
	 * @param  int           $index
	 */
	public function setGroup(ArgGroup $group, $index) {
		$this->group = $group;
		$this->index = $index;
		if ($this->parent) {
			$this->parent->items[ $index ] = $this;
		}
	}

	public function getCValue() {
		return $this->value;
	}

	public function setValue($value) {
		$this->value = $value;
		$this->init();
	}

	public function offsetExists($offset) {
		return false;
	}

	public function offsetGet($offset) {
		if ($offset == 'avaliable') {
			return count($this->values) > 0;
		}

		return false;
	}

	public function offsetSet($offset, $value) {
	}

	public function offsetUnset($offset) {
	}

	public function unsetSubargs(&$r) {
		if ($this->items) {
			foreach ($this->items as $index => $arg) {
				$r[ $index ] = $arg->getDefaultValue();
				$arg->unsetSubargs($r);
			}
		}
	}

	/**
	 * @param string $value
	 *
	 * @return string
	 */
	public function getTitle($value) {
		if ($value == $this->defaultValue) {
			return '';
		}

		return $this->values[ $value ];
	}

	public function getIterator() {
		return new \ArrayIterator($this->values);
	}

	private function init() {
		if (!$this->valueInited) {
			$this->valueInited = true;
			$this->initValues();
			$keys               = array_keys($this->values);
			$this->defaultValue = array_shift($keys);
			unset($keys);
		}
	}
}