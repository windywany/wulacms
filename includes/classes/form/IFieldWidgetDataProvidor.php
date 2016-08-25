<?php
/**
 * default field data providor.
 * @author ngf
 *
 */
interface IFieldWidgetDataProvidor {
	function setOptions($options);
	function getOptionsFormat();
	/**
	 * 到数据.
	 *
	 * @param boolean $option
	 *        	是否是取可用于搜索显示的数据.
	 */
	function getData($option = false);
}