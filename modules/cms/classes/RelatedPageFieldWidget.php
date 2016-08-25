<?php
/**
 * 相关文章输入框.
 * @author Guangfeng
 *
 */
class RelatedPageFieldWidget implements IFieldWidget {
	/*
	 * (non-PHPdoc) @see IFieldWidget::getName()
	 */
	public function getName() {
		return '文章选择';
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidget::getType()
	 */
	public function getType() {
		return 'related_pages';
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidget::render()
	 */
	public function render($definition, $cls = '') {
		$url = tourl ( 'cms/page/browsedialog' );
		$html [] = '<label for="' . $definition ['name'] . '" class="input input-file">';
		$html [] = '<div data-for="#' . $definition ['name'] . '" data-url="' . $url . '" dialog-width="780" dialog-model="true" dialog-title="选择文章" target="dialog" class="button">选择</div>';
		$html [] = '<input type="text" value="' . $definition ['value'] . '" id="' . $definition ['name'] . '" name="' . $definition ['name'] . '"/></label>';
		return implode ( '', $html );
	}
	public function getDataProvidor($options) {
		return new EmptyDataProvidor();
	}
}