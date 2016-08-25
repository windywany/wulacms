<?php
/**
 * 符合标准的页面标题.
 * @author Guangfeng
 *
 */
class DashboardPageTitle implements Renderable {
	private $cls;
	private $icon;
	private $titles = array ();
	/**
	 * 创建一个标题.
	 *
	 * @param string $title        	
	 * @param string $icon        	
	 * @param string $width        	
	 */
	public function __construct($icon = '', $cls = '') {
		$this->icon = $icon;
		$this->cls = $cls;
	}
	public function add($title, $options = array()) {
		$options = ($options instanceof NamedArray) ? $options->toArray () : $options;
		$this->titles [] = array ($title,$options );
	}
	public function render() {
		if ($this->titles) {
			$html [] = '<div class="col-xs-12 col-sm-12 ' . $this->cls . '"><h1 class="page-title txt-color-blueDark">';
			if ($this->icon) {
				$html [] = '<i class="fa fa-fw ' . $this->icon . '"></i>';
			}
			$i = 0;
			foreach ( $this->titles as $title ) {
				list ( $title, $attrs ) = $title;
				
				if ($i > 0) {
					$html [] = '<span>';
					$html [] = '&gt; ';
				}
				if ($attrs) {
					$a = dashboard_htmltag ( 'a', $attrs );
					$a->text ( $title );
					$html [] = $a->render ();
				} else {
					$html [] = $title;
				}
				if ($i > 0) {
					$html [] = '</span>';
				}
				$i ++;
			}
			$html [] = '</h1></div>';
			return implode ( '', $html );
		} else {
			return '';
		}
	}
}