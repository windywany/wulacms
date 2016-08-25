<?php
/**
 * 自定义字段组件接口.
 * @author Guangfeng
 *
 */
interface IFieldWidget {
	/**
	 * 类型.
	 *
	 * @return string widget的类型.
	 */
	public function getType();
	/**
	 * 组件名称 .
	 *
	 *
	 * @return string widget名称.
	 */
	public function getName();	
	/**
	 * get the data providor.
	 * @param array $options options.
	 * @return FieldWidgetDataProvidor
	 */
	public function getDataProvidor($options);
	/**
	 * 绘制控件.
	 *
	 * @param array $definition
	 *        	array('name','value','defaults','required').
	 * @return string html片断.
	 */
	public function render($definition, $cls = '');
}