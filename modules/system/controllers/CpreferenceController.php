<?php
class CpreferenceController extends DefaultPreferencePage {
	protected $checkUser = true;
	protected $acls = array ('index' => 'gm:system/preference','index_post' => 'gm:system/preference' );
	protected function getForm($type) {
		return new GlobalCustomPreferenceForm ();
	}
	protected function getCurrentURL() {
		return tourl ( 'system/cpreference' );
	}
	protected function getPreferenceGroup($type) {
		return 'custom';
	}
	protected function getTitle() {
		return '自定义全局配置';
	}
	protected function supportCustomField() {
		return 'custom';
	}
}
