<?php
abstract class KsDataProvidor {
	const HTML = 'html';
	const ARY = 'array';
	const TEXT = 'text';
	protected $options = array ();
	public function __construct($options = array()) {
		if (is_array ( $options )) {
			$this->options = $options;
		}
	}
	/**
	 * 本数据源提供的数据类型.
	 *
	 * @return string [html|array|text].
	 */
	public abstract function getDataType();
	/**
	 * 获取数据.
	 *
	 * @return mixed 取决于datatype.
	 */
	public abstract function getData();
	/**
	 * 你叫什么名称?
	 */
	public abstract function getName();
	/**
	 * 数据源定义.
	 *
	 * @return AbstractForm 数据源定义选项.
	 */
	public function getConfigFields(&$fields) {
		return null;
	}
	public function getOptions() {
		return $this->options;
	}
}