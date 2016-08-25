<?php
class PreferenceController extends DefaultPreferencePage {
	protected $checkUser = true;
	protected $acls = array ('index' => 'cmt:system/preference','index_post' => 'cmt:system/preference' );
	protected function getCurrentURL() {
		return tourl ( 'comment/preference' );
	}
	protected function getForm($type) {
		return new CommentPreferenceForm ();
	}
	protected function getPreferenceGroup($type) {
		return 'comment';
	}
	protected function getTitle() {
		return '评论&留言设置';
	}
	protected function supportCustomField() {
		return false;
	}
}