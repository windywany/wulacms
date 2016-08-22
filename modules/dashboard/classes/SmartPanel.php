<?php
class SmartPanel implements Renderable {
	private $theme = 'panel-default';
	private $contents = array ();
	public function __construct($theme = 'panel-default') {
		$this->theme = $theme;
	}
	/**
	 * 设置内容.这些内容将以添加的顺序绘制.
	 *
	 * @param Renderable $content
	 *        	要绘制的内容.
	 * @return DashboardPageBody
	 */
	public function content($content) {
		$this->contents [] = $content;
		return $this;
	}
	public function render() {
		if ($this->contents) {
			$html [] = '<div class="panel ' . $this->theme . '">';
			foreach ( $this->contents as $content ) {
				if ($content instanceof Renderable) {
					$html [] = $content->render ();
				} else if (is_string ( $content )) {
					$html [] = $content;
				}
			}
			$html [] = '</div>';
			return implode ( '', $html );
		} else {
			return '';
		}
	}
}