<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
function hook_custom_field_widgets_media($widgets) {
	$widgets->register ( new ImageFieldWidget () );
	$widgets->register ( new MimageFieldWidget () );
}