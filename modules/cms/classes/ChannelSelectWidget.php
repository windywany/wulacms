<?php
/**
 * 栏目选择器.
 * @author Guangfeng
 *
 */
class ChannelSelectWidget implements IFieldWidget, IFieldWidgetDataProvidor {
	private $options;
	/*
	 * (non-PHPdoc) @see IFieldWidget::getName()
	 */
	public function getName() {
		return '栏目选择器';
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidget::getType()
	 */
	public function getType() {
		return 'channel_select';
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidget::render()
	 */
	public function render($definition, $cls = '') {
		$data = $this->getDataProvidor ( $definition ['defaults'] )->getData ();
		$html [] = '<label class="select"><select name="' . $definition ['name'] . '">';
		if (empty ( $definition ['required'] )) {
			$html [] = '<option value="">-请选择栏目-</option>';
		}
		foreach ( $data as $key => $d ) {
			if ($key == $definition ['value']) {
				$html [] = '<option value="' . $key . '" selected="selected">' . $d . '</option>';
			} else {
				$html [] = '<option value="' . $key . '">' . $d . '</option>';
			}
		}
		$html [] = '</select><i></i></label>';
		return implode ( '', $html );
	}
	public function getDataProvidor($options) {
		$this->setOptions ( $options );
		return $this;
	}
	/*
	 * (non-PHPdoc) @see IFieldWidgetDataProvidor::getData()
	 */
	public function getData($option = false) {
		$data = array ();
		if (empty ( $this->options )) {
			dbselect ()->from ( '{cms_channel}' )->treeWhere ( array ('deleted' => 0 ) )->treeKey ( 'refid' )->treeOption ( $data );
			return $data;
		}
		$args = explode ( ',', $this->options );
		$type = $args [0];
		$upid = 0;
		if (! empty ( $type )) {
			$upid = dbselect ()->from ( '{cms_channel}' )->where ( array ('refid' => $type ) )->get ( 'id' );
			if (! $upid) {
				$upid = 0;
			}
		}
		
		$tree = true;
		if (isset ( $args [1] )) {
			$tree = ! empty ( $args [1] );
		}
		if ($tree) {
			$tree = dbselect ()->from ( '{cms_channel}' )->treeKey ( 'refid' )->treeWhere ( array ('deleted' => 0 ) );
			if ($option) {
				$tree->treePad ( false );
			}
			$tree->treeOption ( $data, 'id', 'upid', 'name', null, $upid );
			return $data;
		} else {
			return dbselect ( 'refid,name' )->from ( '{cms_channel}' )->where ( array ('upid' => $upid,'deleted' => 0 ) )->toArray ( 'name', 'refid' );
		}
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidgetDataProvidor::getOptionsFormat()
	 */
	public function getOptionsFormat() {
		return '参数格式:[parent[,<0|1>]].说明-parent:上级目录,0|1表示是否包括下级目录.';
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidgetDataProvidor::setOptions()
	 */
	public function setOptions($options) {
		$this->options = $options;
	}
}