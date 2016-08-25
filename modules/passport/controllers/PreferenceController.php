<?php
class PreferenceController extends DefaultPreferencePage {
	protected $checkUser = true;
	protected $acls = array ('index' => 'pst:system/preference','index_post' => 'pst:system/preference' );
	public function styles($p) {
		$data ['more'] = false;
		$styles = PassportPreferenceForm::getStyles ( $p );
		$data ['results'] = array ();
		foreach ( $styles as $id => $v ) {
			$data ['results'] [] = array ('id' => $id,'text' => $v );
		}
		return new JsonView ( $data );
	}
	protected function getCurrentURL() {
		return tourl ( 'passport/preference' );
	}
	protected function getForm($type) {
		if ($type == 'base') {
			return new PassportPreferenceForm ();
		}
		$groups = $this->getGroups ();
		if (isset ( $groups [$type] ) && ! empty ( $groups [$type] )) {
			$cls = ucfirst ( $type ) . 'PreferenceForm';
			return new $cls ();
		}
		
		return new PassportPreferenceForm ();
	}
	protected function getPreferenceGroup($type) {
		if ($type == 'base') {
			return 'passport';
		}
		$groups = $this->getGroups ();
		if (isset ( $groups [$type] ) && ! empty ( $groups [$type] )) {
			return $type;
		}
		return 'passport';
	}
	protected function getTitle() {
		return '通行证设置';
	}
	protected function getGroups() {
		$groups = array ('base' => '基本设置' );
		$groups = apply_filter ( 'get_passport_setting_groups', $groups );
		return $groups;
	}
}