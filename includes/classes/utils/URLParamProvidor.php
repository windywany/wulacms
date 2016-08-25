<?php
/**
 * URL参数数据提供器.
 * @author leo
 *
 */
class URLParamProvidor implements IteratorAggregate {
	protected $data;
	protected $text;
	protected $value = null;
	protected $parent = null;
	protected $emptyText = '不限';
	protected $emptyVal = '';
	protected $parentValue = null;
	protected $var = '';
	protected $active = false;
	protected $prefix = '';
	/**
	 * 创建一个URL参数.
	 *
	 * @param string $var
	 *        	要替换的变量名.
	 * @param string $value
	 *        	字段当前值.
	 * @param array $data
	 *        	字段可用值,为 key=>value对. key代表值,value代表显示.
	 * @param string $text
	 *        	字段显示文本.
	 * @param URLParamProvidor $parent
	 *        	父级.
	 */
	public function __construct($var, $value = null, $data = null, $text = '', $parent = null) {
		$this->value = $value;
		$this->data = $data;
		$this->text = $text;
		$this->var = $var;
		$this->setParent ( $parent );
	}
	public function isActive($active) {
		$this->active = $active;
	}
	public function setEmptyText($text) {
		$this->emptyText = $text;
	}
	public function setParent($parent) {
		if ($parent instanceof URLParamProvidor) {
			$this->parent = $parent;
			$this->parentValue = $parent->getValue ();
			$this->emptyVal = $parent->getURLValue ( $this->parentValue );
		}
	}
	public function setParentValue($value) {
		$this->parentValue = $value;
	}
	public function setPrefix($prefix) {
		$this->prefix = $prefix;
	}
	public function setData($data) {
		$this->data = $data;
	}
	public function setValue($value) {
		$this->value = $value;
	}
	public function setTexft($text) {
		$this->text = $text;
	}
	public function getText() {
		return $this->text;
	}
	public function getVar() {
		return $this->var;
	}
	public function getURLValue($value = null) {
		if (is_null ( $value )) {
			$v = $this->value;
		} else {
			$v = $value;
		}
		if ($v) {
			return $this->prefix . $v;
		}
		return '';
	}
	public function getValue() {
		return $this->value;
	}
	public function selected($value) {
		return $this->value == $value;
	}
	public function __get($name) {
		$name = strtolower ( $name );
		if ($name == 'value') {
			return $this->value;
		} else if ($name == 'text') {
			return $this->text;
		} else if ($name == 'available') {
			return $this->isAvailable ();
		} else if ($name == 'active') {
			return $this->active;
		}
		return null;
	}
	/**
	 * 是否有值.
	 */
	public function isAvailable() {
		if ($this->parent && null == $this->parent->getValue ()) {
			return false;
		}
		return true;
	}
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see IteratorAggregate::getIterator()
	 */
	public function getIterator() {
		if (empty ( $this->data )) {
			$this->data = $this->getData ();
		}
		if (is_array ( $this->data )) {
			return new ArrayIterator ( $this->data );
		} else {
			return new ArrayIterator ( array () );
		}
	}
	protected function getData() {
		return array ();
	}
}