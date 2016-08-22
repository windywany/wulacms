<?php
class SmartJarvisWidget extends NamedArray implements Renderable {
	private $tag;
	private $header;
	private $body;
	private $content;
	private $bodyToolbar;
	public function __construct($id, $title, $icon = 'fa-th-large') {
		$this->tag = dashboard_htmltag ( 'div' )->cls ( 'jarviswidget' )->id ( 'jid-' . $id );
		$this->attrs ['data-widget-editbutton'] = "false";
		$this->attrs ['data-widget-togglebutton'] = "false";
		$this->attrs ['data-widget-deletebutton'] = "false";
		$this->attrs ['data-widget-fullscreenbutton'] = "false";
		$this->attrs ['data-widget-custombutton'] = "false";
		$this->attrs ['data-widget-collapsed'] = "false";
		$this->attrs ['data-widget-sortable'] = "false";
		$this->header = dashboard_htmltag ( 'header' )->role ( 'content' );
		if ($icon) {
			$this->header->child ( '<span class="widget-icon"><i class="fa ' . $icon . '"></i></span>' );
		}
		$this->header->child ( '<h2> ' . $title . ' </h2>' );
		$this->tag->child ( $this->header );
		$this->body = dashboard_htmltag ( 'div' )->role ( 'content' );
		$this->content = dashboard_htmltag ( 'div' );
		$this->body->child ( $this->content );
		$this->tag->child ( $this->body );
	}
	public function custombutton($custombutton = false) {
		if ($custombutton) {
			unset ( $this->attrs ['data-widget-custombutton'] );
		} else {
			$this->attrs ['data-widget-custombutton'] = "false";
		}
		return $this;
	}
	public function deletebutton($deletebutton = false) {
		if ($deletebutton) {
			unset ( $this->attrs ['data-widget-deletebutton'] );
		} else {
			$this->attrs ['data-widget-deletebutton'] = "false";
		}
		return $this;
	}
	public function togglebutton($togglebutton = false) {
		if ($togglebutton) {
			unset ( $this->attrs ['data-widget-togglebutton'] );
		} else {
			$this->attrs ['data-widget-togglebutton'] = "false";
		}
		return $this;
	}
	public function editbutton($editbutton = false) {
		if ($editbutton) {
			unset ( $this->attrs ['data-widget-editbutton'] );
		} else {
			$this->attrs ['data-widget-editbutton'] = "false";
		}
		return $this;
	}
	public function collapsed($collapsed = false) {
		if ($collapsed) {
			unset ( $this->attrs ['data-widget-collapsed'] );
		} else {
			$this->attrs ['data-widget-collapsed'] = "false";
		}
		return $this;
	}
	public function sortable($sortable = false) {
		if ($sortable) {
			unset ( $this->attrs ['data-widget-sortable'] );
		} else {
			$this->attrs ['data-widget-sortable'] = "false";
		}
		return $this;
	}
	public function fullscreenbutton($fullscreenbutton = false) {
		if ($fullscreenbutton) {
			unset ( $this->attrs ['data-widget-fullscreenbutton'] );
		} else {
			$this->attrs ['data-widget-fullscreenbutton'] = "false";
		}
		return $this;
	}
	public function edit($edit) {
	}
	public function toolbar($toolbar) {
		$tag = dashboard_htmltag ( 'div' )->cls ( 'widget-toolbar' )->role ( 'menu' );
		$tag->child ( $toolbar );
		$this->header->child ( $tag );
		return $this;
	}
	public function head($head) {
		$this->header->child ( $head );
		return $this;
	}
	public function body($body) {
		$this->content->child ( $body );
		return $this->content;
	}
	public function bodytoolbar($toolbar) {
		if (! $this->bodyToolbar) {
			$this->bodyToolbar = dashboard_htmltag ( 'div' )->cls ( 'widget-body-toolbar' );
			$this->content->unshift ( $this->bodyToolbar );
		}
		$this->bodyToolbar->child ( $toolbar );
	}
	public function render() {
		$this->tag->combine_array ( $this->attrs );
		$cls = 'widget-body ' . $this->content->get ( 'class' );
		$this->content->cls ( $cls );
		return $this->tag->render ();
	}
}