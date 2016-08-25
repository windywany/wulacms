<?php
class CmsMultiPageSelectWidget implements IFieldWidget, IFieldWidgetDataProvidor {
	private $options;
	public function getDataProvidor($options) {
		$this->setOptions ( $options );
		return $this;
	}
	public function getType() {
		return 'cms_multi_select';
	}
	public function getName() {
		return '页面多选器';
	}
	public function render($definition, $cls = '') {
		$model = $definition ['defaults'];
		if (empty ( $model )) {
			$model = 'news';
		}
		$value = isset ( $definition ['value'] ) ? $definition ['value'] : '';
		$data = $this->getValus ( $value );
		$id = isset ( $definition ['id'] ) ? $definition ['id'] : $definition ['name'];
		$text = isset ( $definition ['text'] ) ? $definition ['text'] : '选择';
		$title = empty ( $definition ['title'] ) ? '请选择' : $definition ['title'];
		$width = empty ( $definition ['width'] ) ? '450' : $definition ['width'];
		$url = tourl ( 'cms/page/browsedialog/0' ) . $model;
		$html [] = '<label class="input input-file" for="' . $id . '"><div class="button" target="dialog" dialog-model="true" data-for="#' . $id . '"';
		$html [] = ' dialog-title="' . $title . '"';
		$html [] = ' dialog-width="' . $width . '"';
		$html [] = ' data-url="' . $url . '"';
		$html [] = '>';
		$html [] = $text . '</div>';
		$html [] = '<input type="text" readonly="readonly"/>';
		$html [] = '</label>';
		
		$html [] = '<div class="well well-sm item-removable" data-widget="nuiTagWrapper">';
		$html [] = '<input type="hidden" id="' . $id . '" name="' . $definition ['name'] . '" value="' . $value . '"/><div class="tags-wrapper">';
		if ($data) {
			foreach ( $data as $d ) {
				$html [] = '<span class="badge removable">' . $d ['title2'] . '<a class="close" data-value="' . $d ['id'] . '">&times;</a></span>';
			}
		}
		$html [] = '</div><b class="clearfix"/>';
		$html [] = '</div>';
		return implode ( '', $html );
	}
	public function getData($option = false) {
		if ($option) {
			$model = $this->options;
			if ($model) {
				return dbselect ( 'title2,id' )->from ( '{cms_page}' )->where ( array ('hidden' => 0,'deleted' => 0,'model'=>$model ) )->toArray ( 'title2', 'id' );
			}
		}
		return array ();
	}
	public function setOptions($options) {
		$this->options = $options;
	}
	public function getOptionsFormat() {
		return '参数格式为：[model_name]。model_name为页面模型名.';
	}
	private function getValus($values) {
		$ids = safe_ids ( $values, ',', true );
		$data = false;
		if ($ids) {
			$data = dbselect ( '*' )->from ( '{cms_page}' )->where ( array ('id IN' => $ids ) )->toArray ();
		}
		return $data;
	}
}