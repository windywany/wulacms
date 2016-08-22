<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 * 注册自定义表单字段.
 *
 * @param CustomeFieldWidgetRegister $widgets        	
 */
function hook_for_get_custom_field_widgets($widgets) {
	$widgets->register ( new ModelSelectWidget () );
	$widgets->register ( new BlockItemSelectWidget () );
	$widgets->register ( new ChannelSelectWidget () );
	$widgets->register ( new RelatedPageFieldWidget () );
	$widgets->register ( new ChunkFieldWidget () );
	$widgets->register ( new CmsCatalogSelectWidget () );
	$widgets->register ( new CmsSinglePageSelectWidget () );
	$widgets->register ( new CmsMultiPageSelectWidget () );
	$widgets->register ( new CmsPageSelectWidget () );
}