<?php
/**
 * 后台页面.
 * @author Guangfeng
 *
 */
class DashboardPage implements Renderable {
	private $title;
	private $toolbar;
	private $content;
	private $header;
	private $footer;
	private $titleIcon;
	private $titleCls;
	private $toolbarCls;
	
	/**
	 * 创建一个{@link DashboardPage}实例.
	 *
	 * @param string $icon        	
	 * @param string $titleCls        	
	 * @param string $toolbarCls        	
	 */
	public function __construct($icon = '', $titleCls = 'col-md-8 col-lg-8 hidden-xs hidden-sm', $toolbarCls = 'col-md-4 col-lg-4') {
		$this->titleIcon = $icon;
		$this->titleCls = $titleCls;
		$this->toolbarCls = $toolbarCls;
		$this->initContents ();
	}
	/**
	 * 设置标题.
	 *
	 * @param string $title        	
	 * @param NamedArray|array $options        	
	 * @return DashboardPage
	 */
	public function title($title, $options = array()) {
		if (! $this->title) {
			$this->title = new DashboardPageTitle ( $this->titleIcon, $this->titleCls );
		}
		$this->title->add ( $title, $options );
		return $this;
	}
	/**
	 * 设置工具栏.
	 *
	 * @param HtmlTagElm|string $text        	
	 * @param string $icon        	
	 * @param string $theme        	
	 * @return HtmlTagElm
	 */
	public function toolbar($text, $icon = '', $theme = '') {
		if (! $this->toolbar) {
			$this->toolbar = new DashboardPageToolbar ( $this->toolbarCls );
		}
		if (is_string ( $text )) {
			$button = dashboard_htmltag ( 'button' )->cls ( 'btn btn-labeled ' . $theme )->text ( $text, true );
			$span = dashboard_htmltag ( 'span' )->cls ( 'btn-label' );
			$icon = dashboard_htmltag ( 'i' )->cls ( $icon );
			$span->child ( $icon );
			$button->child ( $span );
			return $this->toolbar->item ( $button );
		} else if ($text instanceof Renderable) {
			return $this->toolbar->item ( $text );
		}
		return null;
	}
	/**
	 * 设置正文之前的内容.
	 *
	 * @param Renderable $head        	
	 * @param int $row        	
	 * @param int $col        	
	 * @param string $width        	
	 * @return DashboardPage
	 */
	public function head($head, $row = 1, $col = 1, $width = 'col-sm-12') {
		if (! $this->header) {
			$this->header = new DashboardUIManager ();
		}
		$cell = $this->header->setCell ( $row, $col, $head, $width );
		return $this;
	}
	/**
	 * 设置正文之后的内容.
	 *
	 * @param Renderable $foot        	
	 * @param int $row        	
	 * @param int $col        	
	 * @param string $width        	
	 * @return DashboardPage
	 */
	public function foot($foot, $row = 1, $col = 1, $width = 'col-sm-12') {
		if (! $this->footer) {
			$this->footer = new DashboardUIManager ();
		}
		$cell = $this->footer->setCell ( $row, $col, $foot, $width );
		return $this;
	}
	/**
	 * 设置正文内容.
	 *
	 * @param Renderable $body        	
	 * @param int $row        	
	 * @param int $col        	
	 * @param string $width        	
	 * @return DashboardPage
	 */
	public function body($body, $row = 1, $col = 1, $width = 'col-sm-12') {
		if (! $this->content) {
			$this->content = new DashboardUIManager ();
		}
		$cell = $this->content->setCell ( $row, $col, $body, $width );
		return $this;
	}
	/*
	 * (non-PHPdoc) @see Renderable::render()
	 */
	public function render() {
		$html [] = '<div class="row">';
		if ($this->title) {
			$html [] = $this->title->render ();
		}
		if ($this->toolbar) {
			$html [] = $this->toolbar->render ();
		}
		$html [] = '</div>';
		if ($this->header) {
			$html [] = "\n";
			$html [] = $this->header->render ();
		}
		if ($this->content) {
			$html [] = "\n";
			$html [] = '<section id="widget-grid">';
			$html [] = $this->content->render ();
			$html [] = '</section>';
		}
		if ($this->footer) {
			$html [] = "\n";
			$html [] = $this->footer->render ();
		}
		$footer = $this->footer ();
		if ($footer) {
			$html [] = "\n";
			$html [] = $footer;
		}
		return implode ( '', $html );
	}
	/**
	 * 返回此页面对应的view.
	 *
	 * @param string $tpl        	
	 * @param array $data        	
	 * @return SmartyView
	 */
	public function view($tpl = null, $data = array()) {
		$data ['dashboard_page'] = $this;
		if ($tpl) {
			return view ( $tpl, $data );
		} else {
			return view ( '@dashboard/views/page.tpl', $data );
		}
	}
	/**
	 * 初始化页面组件.
	 */
	protected function initContents() {
	}
	/**
	 * 返回底部.
	 *
	 * @return string
	 */
	protected function footer() {
		return false;
	}
}