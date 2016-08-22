<?php
/**
 * 选择区块.
 * @author Guangfeng
 *
 */
class BlockItemSelectWidget implements IFieldWidget {
	/*
	 * (non-PHPdoc) @see IFieldWidget::getName()
	 */
	public function getName() {
		return '区块选择';
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidget::getType()
	 */
	public function getType() {
		return 'BlockItemSelect';
	}
	
	/*
	 * (non-PHPdoc) @see IFieldWidget::render()
	 */
	public function render($definition, $cls = '') {
		$options = array ('--请选择分类--' );
		dbselect ()->from ( '{cms_catelog}' )->treeWhere ( array ('deleted' => 0,'type' => 'block' ) )->treeOption ( $options );
		$dataURL = tourl ( 'cms/block/select_data' );
		?>
<div class="row">
	<div class="col col-xs-12 col-md-6">
		<label class="select"> <select style="width: 100%"
			data-widget="nuiCombox" id="<?php echo $definition['name']?>_catelog"
			name="<?php echo $definition['name']?>_catelog">
<?php foreach ($options as $id => $op):?>
			<option value="<?php echo $id?>"><?php echo $op?></option>
<?php endforeach;?>
			</select>
		</label>
	</div>
	<div class="col col-xs-12 col-md-6">
		<label class="input"> <input data-widget="nuiCombox"
			data-source="<?php echo $dataURL?>"
			data-parent="<?php echo $definition['name']?>_catelog" type="hidden"
			style="width: 100%" name="<?php echo $definition['name']?>"
			id="<?php echo $definition['name']?>" />
		</label>
	</div>
</div>
<?php
	}
	public function getDataProvidor($options) {
		return new EmptyDataProvidor ();
	}
}