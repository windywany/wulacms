<?php
/**
 * 分页组件.
 * @author ngf
 *
 */
class SmartPager extends NamedArray implements Renderable {
	/**
	 * 创建一个{@link DashboardPager}实例.
	 *
	 * @param string $for        	
	 * @param int $limit        	
	 */
	public function __construct($for, $limit = '10') {
		$this->attrs ['data-for'] = $for;
		$this->attrs ['data-limit'] = $limit;
	}
	/*
	 * (non-PHPdoc) @see Renderable::render()
	 */
	public function render() {
		if ($this->attrs) {
			$html [] = '<div class="panel-footer">';
			$this->attrs ['data-widget'] = 'nuiPager';
			if (! isset ( $this->attrs ['data-limit'] )) {
				$this->attrs ['data-limit'] = '10';
			}
			$pager = dashboard_htmltag ( 'div', $this->attrs );
			$html [] = $pager->render ();
			$html [] = '</div>';
			return implode ( '', $html );
		} else {
			return '';
		}
	}
}