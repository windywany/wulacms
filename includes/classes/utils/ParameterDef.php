<?php

/**
 * 参数定义类，用于快速给方法提供准确参数.
 * 只需定义public属性即可。
 */
abstract class ParameterDef {
	private $__vars = [];

	public function __construct() {
		$obj  = new ReflectionObject($this);
		$vars = $obj->getProperties(ReflectionProperty::IS_PUBLIC);
		foreach ($vars as $var) {
			$this->__vars[] = $var->getName();
		}
	}

	/**
	 * 获取参数列表.
	 * @return array
	 */
	public function toArray() {
		$ary = [];
		foreach ($this->__vars as $var) {
			$value = $this->{$var};
			if (is_null($value)) {
				continue;
			}
			$ary[ $var ] = $value;
		}

		unset($obj, $vars, $var);

		return $ary;
	}
}