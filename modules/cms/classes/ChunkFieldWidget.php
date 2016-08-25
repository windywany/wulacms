<?php
/**
 * 碎片选择.
 * @author Guangfeng
 *
 */
class ChunkFieldWidget implements IFieldWidget{
	/* (non-PHPdoc)
	 * @see IFieldWidget::getName()
	 */
	public function getName() {
		return '碎片选择';		
	}

	/* (non-PHPdoc)
	 * @see IFieldWidget::getType()
	 */
	public function getType() {
		return 'chunk';
	}

	/* (non-PHPdoc)
	 * @see IFieldWidget::render()
	 */
	public function render($definition, $cls = '') {
		$name = $definition['name'];
		$value = $definition['value'];
		$url = tourl('cms/chunk/auto_chunk');
?>
<input type="hidden" data-widget="nuiCombox"
	style="width:100%"
	data-source="<?php echo $url?>"
	value="<?php echo $value?>" id="<?php echo $name?>" name="<?php echo $name?>"/>
<?php
	}	
	public function getDataProvidor($options) {
		return new EmptyDataProvidor();
	}
}