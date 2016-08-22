<?php
class EmptyDataProvidor implements IFieldWidgetDataProvidor {
	public function getData($option = false) {
		return '';
	}
	public function setOptions($options) {
	}
	public function getOptionsFormat() {
		return "无";
	}
}