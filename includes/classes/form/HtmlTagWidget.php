<?php
/**
 * 自定义的使用html定义的widget.
 * @author ngf
 *
 */
class HtmlTagWidget implements IFieldWidget {
	public function getDataProvidor($options) {
		return new EmptyDataProvidor ();
	}
	public function getType() {
		return 'htmltag';
	}
	public function getName() {
		return false;
	}
	public function render($definition, $cls = '') {
		$defaults = $definition ['defaults'];
		if ($defaults instanceof Renderable) {
			return $defaults->render ();
		} else {
			return $defaults;
		}
	}
}