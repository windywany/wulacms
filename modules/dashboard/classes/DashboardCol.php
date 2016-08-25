<?php
/**
 * Dashboard中的一列.
 * @author Guangfeng
 *
 */
class DashboardCol {
	private $widgets = array ();
	private $colWidth = 'col-sm-12';
	public function __construct($width = 'col-sm-12') {
		$this->setWidth ( $width );
	}
	/**
	 * 添加一个小组件.
	 *
	 * @param Renderable $widget
	 *        	可绘制的组件或字符串.
	 * @return DashboardCol
	 */
	public function addWidget($widget) {
		$this->widgets [] = $widget;
		return $this;
	}
	/**
	 * 设置列的宽度.
	 *
	 * @param string $width
	 *        	宽度.
	 * @return DashboardCol
	 */
	public function setWidth($width) {
		if (! empty ( $width )) {
			$this->colWidth = $width;
		}
	}
	public function render() {
		$html = array ();
		$html [] = '<div class="' . $this->colWidth . '">';
		foreach ( $this->widgets as $w ) {
			if ($w instanceof Renderable) {
				$html [] = $w->render ();
			} else if (is_string ( $w )) {
				$html [] = $w;
			}
		}
		$html [] = '</div>';
		return implode ( '', $html );
	}
}