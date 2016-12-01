<?php

/**
 * 参数定义类，用于快速给方法提供准确参数.
 * 只需定义public属性即可。
 */
abstract class ParameterDef {
	/**
	 * 获取参数列表.
	 * @return array
	 */
	public function toArray() {
		$obj  = new ReflectionObject($this);
		$vars = $obj->getProperties(ReflectionProperty::IS_PUBLIC);
		$ary  = [];
		foreach ($vars as $var) {
			$name  = $var->getName();
			$value = $var->getValue($obj);

			if (is_null($value)) {
				continue;
			}
			$ary[ $name ] = $value;
		}
		unset($obj, $vars, $var);

		return $ary;
	}
}