<?php
class SmartGrid extends NamedArray implements Renderable {
	private $fields = array ();
	private $grid;
	private $thead;
	private $theadTr;
	private $data;
	private $total;
	private $name = '';
	private $fieldID = '';
	private $checkboxEnabled = true;
	private $pagerObj = null;
	public function __construct($id, $data = array(), $total = '') {
		$this->attrs ['id'] = $id;
		$this->thead = dashboard_htmltag ( 'thead' );
		$this->theadTr = dashboard_htmltag ( 'tr' );
		$this->thead->child ( $this->theadTr );
		$this->data = $data;
		$this->total = $total;
	}
	/**
	 * 启用或禁用全选框.
	 *
	 * @param bool $enable        	
	 */
	public function enableCheckbox($enable = true) {
		$this->checkboxEnabled = $enable;
	}
	/**
	 * 分页.
	 *
	 * @param int $limit        	
	 * @return SmartGrid
	 */
	public function pager($limit = 20) {
		$this->pagerObj = new SmartPager ( '#' . $this->attrs ['id'], $limit );
		return $this;
	}
	/**
	 * 设置行变量.
	 *
	 * @param string $name        	
	 * @param string $field        	
	 * @return SmartGrid
	 */
	public function varname($name, $field) {
		$this->name = $name;
		$this->fieldID = $field;
		return $this;
	}
	/**
	 *
	 * @param string $text        	
	 * @param int $width        	
	 * @param string $field        	
	 * @return HtmlTagElm
	 */
	public function head($text, $width = null, $field = null) {
		$head = dashboard_htmltag ( 'th' );
		$head->width ( $width );
		if ($text instanceof Renderable) {
			$head->child ( $text );
		} else {
			$head->text ( $text );
		}
		$this->theadTr->child ( $head );
		$this->fields [] = $field;
		return $head;
	}
	public function render() {
		if (! isset ( $this->attrs ['data-widget'] )) {
			$this->attrs ['data-widget'] = 'nuiTable';
		}
		$this->grid = dashboard_htmltag ( 'table', $this->attrs );
		if ($this->checkboxEnabled) {
			$head = dashboard_htmltag ( 'th' );
			$head->width ( 30 );
			$head->child ( dashboard_htmltag ( 'input' )->cls ( 'grp' )->type ( 'checkbox' ) );
			$this->theadTr->unshift ( $head );
		}
		$this->grid->child ( $this->thead );
		if ($this->data) {
			$this->grid->child ( new SmartGridBody ( $this->fields, $this->data, $this->total, $this->name, $this->fieldID, $this->checkboxEnabled ) );
		}
		$html = $this->grid->render ();
		if ($this->pagerObj) {
			$html .= $this->pagerObj->render ();
		}
		return $html;
	}
}
