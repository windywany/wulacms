<?php
abstract class KsWidgetView {
	protected $options = array ();
	protected $parent;
	protected $parentData;
	public function __construct($options = array()) {
		$dopts = $this->getDefaultOptions ();
		if (is_array ( $options )) {
			$this->options = $options;
		}
		if (is_array ( $dopts )) {
			$this->options = array_merge ( $dopts, $this->options );
		}
	}
	/**
	 * 取配置表单.
	 *
	 * @param AbstractForm $fields        	
	 */
	public function getConfigFields(&$fields) {
		// sub class needs to implement it.
	}
	public function getOptions() {
		return $this->options;
	}
	public function get($name, $default = '') {
		if (isset ( $this->options [$name] )) {
			return $this->options [$name];
		}
		return $default;
	}
	public function setParent($parent) {
		$this->parent = $parent;
		if ($this->parent && ! $this->parentData) {
			$this->parentData = $this->parent->getData ();
		}
	}
	public function getParentData() {
		return $this->parentData ? $this->parentData : array ();
	}
	protected function getDefaultOptions() {
		return null;
	}
	/**
	 * 支持的数据类型.
	 */
	public abstract function supportDataType();
	/**
	 * 取模板视图.
	 *
	 * @param array $conf
	 *        	配置.
	 * @return View 模板视图.
	 */
	public abstract function getView();
	/**
	 * 你叫什么名称?
	 */
	public abstract function getName();
}
