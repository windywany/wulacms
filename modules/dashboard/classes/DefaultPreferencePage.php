<?php
abstract class DefaultPreferencePage extends Controller {
	protected $checkUser = true;
	public function index($_g = 'base') {
		if (! $this->icando ( $this->user )) {
			Response::showErrorMsg ( '你无权进行' . $this->getTitle () );
		}
		$data ['groups'] = $this->getGroups ();
		
		if (! isset ( $data ['groups'] [$_g] )) {
			Response::respond ( 404 );
		}
		$data ['crules'] = '{}';
		
		$form = $this->getForm ( $_g );
		$data ['_g'] = $_g;
		$data ['rules'] = $form->rules ();
		$data ['form'] = $form;
		$data ['formName'] = $form->getName();
		$data ['title'] = $this->getTitle ();
		$data ['p_url'] = $this->getCurrentURL ();
		$data ['scripts'] = $form->getScripts ();
		$data ['cfields'] = array ();
		$pg = $this->getPreferenceGroup ( $_g );
		$values = dbselect ( 'name,value' )->from ( '{preferences}' )->where ( array ('preference_group' => $pg ) )->toArray ( 'value', 'name' );
		$data ['customEnabled'] = $this->supportCustomField ();
		if ($data ['customEnabled']) {
			$cform = new CustomCfgFieldForm ();
			$data ['crules'] = $cform->rules ();
			$cfields = dbselect ( 'value' )->from ( '{preferences}' )->where ( array ('name' => 'custom_fields','preference_group' => $pg ) )->get ( 'value' );
			if ($cfields) {
				$cfields = @unserialize ( $cfields );
			}
			
			if ($cfields && $values) {
				foreach ( $values as $name => $v ) {
					if (isset ( $cfields [$name] )) {
						$hook = 'parse_' . $cfields [$name] ['type'] . '_field_value';
						if (has_hook ( $hook )) {
							$values [$name] = apply_filter ( $hook, $v );
						}
					}
				}
			}
			if ($cfields) {
				$data ['cfields'] = array_keys ( $cfields );
			}
		}
		$data ['widgets'] = new DefaultFormRender ( $form->buildWidgets ( $values ) );
		$data ['cfields'] = json_encode ( $data ['cfields'] );
		$tpl = $this->getTemplate ( $_g );
		return view ( $tpl, $data );
	}
	public function index_post($_g = 'base') {
		if (! $this->icando ( $this->user )) {
			Response::showErrorMsg ( '你无权进行' . $this->getTitle () );
		}
		$data ['groups'] = $this->getGroups ();
		if (! isset ( $data ['groups'] [$_g] )) {
			Response::respond ( 404 );
		}
		$form = $this->getForm ( $_g );
		
		$cfgs = $form->valid ();
		if ($cfgs) {
			$data ['customEnabled'] = $this->supportCustomField ();
			$cfields = array ();
			if ($data ['customEnabled']) {
				$cfields = dbselect ( 'value' )->from ( '{preferences}' )->where ( array ('name' => 'custom_fields','preference_group' => $data ['customEnabled'] ) )->get ( 'value' );
				if ($cfields) {
					$cfields = @unserialize ( $cfields );
				}
				if ($cfields === false) {
					$cfields = array ();
				}
			}
			
			$time = time ();
			$uid = $this->user->getUid ();
			$datas = array ();
			$pg = $this->getPreferenceGroup ( $_g );
			$cfgs = apply_filter ( 'before_save_preference_' . $pg, $cfgs );
			foreach ( $cfgs as $name => $value ) {
				if (isset ( $cfields [$name] )) {
					$hook = 'alter_' . $cfields [$name] ['type'] . '_field_value';
					if (has_hook ( $hook )) {
						$value = apply_filter ( $hook, $value, $name );
					}
				}
				$data = array ();
				$data ['preference_group'] = $pg;
				$data ['name'] = $name;
				$cfg = dbselect ( 'preference_id,value' )->from ( '{preferences}' )->where ( $data )->get ();
				if ($cfg && $cfg ['value'] != $value) {
					$data ['value'] = $value;
					$data ['update_time'] = $time;
					$data ['user_id'] = $uid;
					unset ( $cfg ['value'] );
					dbupdate ( '{preferences}' )->set ( $data )->where ( $cfg )->exec ();
				} else if (! $cfg) {
					$data ['value'] = $value;
					$data ['update_time'] = $time;
					$data ['user_id'] = $uid;
					$datas [] = $data;
				}
			}
			if ($datas) {
				dbinsert ( $datas, true )->into ( '{preferences}' )->exec ();
			}
			
			$rtn = apply_filter ( 'on_preference_' . $pg . '_saved', NuiAjaxView::refresh ( "设置已保存." ), $cfgs );
			RtCache::delete ( 'system_preferences' );
			cfg ( '', '', true );
			return $rtn;
		} else {
			return NuiAjaxView::validate ( get_class ( $form ), '数据格式不正确，请重新填写.', $form->getErrors () );
		}
	}
	protected function getGroups() {
		return array ('base' => '基本设置' );
	}
	protected function getTitle() {
		return '设置';
	}
	protected function icando($user) {
		return true;
	}
	protected function getTemplate($type) {
		return '@dashboard/views/preference.tpl';
	}
	protected function supportCustomField() {
		return false;
	}
	abstract protected function getPreferenceGroup($type);
	abstract protected function getForm($type);
	abstract protected function getCurrentURL();
}