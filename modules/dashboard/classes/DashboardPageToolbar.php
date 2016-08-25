<?php
class DashboardPageToolbar implements Renderable {
	private $cls;
	private $items = array ();
	public function __construct($cls = '') {
		$this->cls = $cls;
	}
	/**
	 * 添加一个html tag.
	 *
	 * @param HtmlTagElm $item        	
	 * @return HtmlTagElm
	 */
	public function item($item) {
		$this->items [] = $item;
		return $this;
	}
	public function render() {
		if ($this->items) {
			$html [] = '<div class="col-xs-12 col-sm-12 ' . $this->cls . '"><div class="pull-right margin-top-5 margin-bottom-5">';
			foreach ( $this->items as $item ) {
				if ($item instanceof Renderable) {
					$html [] = $item->render ();
				}
			}
			$html [] = '</div></div>';
			return implode ( '', $html );
		} else {
			return '';
		}
	}
}