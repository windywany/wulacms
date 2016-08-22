<?php
/**
 * 默认的基于SmartAdmin样式的表单绘制器.
 * @author ngf
 *
 */
class DefaultFormRender implements Renderable {
	private $widgets;
	public function __construct($widgets) {
		$this->widgets = $widgets;
	}
	/**
	 * 绘制.
	 *
	 * @return string
	 */
	public function render() {
		if ($this->widgets) {
			$chunks = array ();
			foreach ( $this->widgets as $widget ) {
				if (is_object ( $widget )) {
					$chunks [] = $widget->render ( $this );
				} else {
					$chunks [] = $this->renderWidget ( $widget );
				}
			}
			return implode ( '', $chunks );
		} else {
			return '';
		}
	}
	/**
	 * 取所有组件。
	 *
	 * @return array 本绘制器内的所有组件.
	 */
	public function getWidgets() {
		return $this->widgets;
	}
	/**
	 * 绘制单个组件.
	 *
	 * @param array $widget        	
	 * @param string $col        	
	 * @return string
	 */
	public function renderWidget($widget, $col = null) {
		$type = $widget ['widget']->getType ();
		if ($type == 'hidden') {
			return $widget ['widget']->render ( $widget );
		} else {
			$chunks [] = '<section';
			if ($col) {
				$chunks [] = ' class="col col-' . $col . '"';
			}
			$chunks [] = '>';
			if (isset($widget ['label'])) {
				$chunks [] = '<label class="label" for="'.$widget ['id'].'">' . $widget ['label'] . '</label>';
			}
			$chunks [] = $widget ['widget']->render ( $widget );
			if (isset ( $widget ['note'] ) && $widget ['note']) {
				$chunks [] = '<div class="note">' . $widget ['note'] . '</div>';
			}
			$chunks [] = '</section>';
			return implode ( '', $chunks );
		}
	}
}