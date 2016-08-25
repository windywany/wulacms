<?php
/**
 * 通用设置控制器.
 *
 * @author Guangfeng
 */
class PreferenceController extends Controller {
	private $defaultGroup = array ('name' => '通用设置','icon' => 'fa-cog','form' => 'PreferenceForm' );
	private $bGroups = array ();
	protected $checkUser = true;
	protected $acls = array ('index' => 'gm:system/preference','index_post' => 'gm:system/preference','custom' => 'gm:system/preference','custom_post' => 'gm:system/preference','delf' => 'gm:system/preference' );
	/*
	 * (non-PHPdoc) @see Controller::preRun()
	 */
	public function preRun($method) {
		parent::preRun ( $method );
		$this->bGroups ['core'] = $this->defaultGroup;
		$this->bGroups ['corepst'] = array ('name' => '通行证接入','icon' => 'fa-cog','form' => 'PassportClientPreferenceForm' );
		$this->bGroups ['smtp'] = array ('name' => '邮箱设置','icon' => 'fa-cog','form' => 'MailSettingForm' );
		$this->bGroups = apply_filter ( 'get_preference_group', $this->bGroups );
	}
	/**
	 * 通用设置。
	 *
	 * @return SmartyView
	 */
	public function index($_g = 'core') {
		$groups = $this->bGroups;
		if (isset ( $groups [$_g] )) {
			$group = $groups [$_g];
		} else {
			$group = $this->defaultGroup;
			$_g = 'core';
		}
		if (is_subclass_of2 ( $group ['form'], 'AbstractForm' )) {
			$form = new $group ['form'] ();
		} else {
			$form = new DynamicForm ( $group ['form'] );
		}
		$data ['rules'] = $form->rules ();
		$data ['form'] = $form;
		$data ['_g'] = $_g;
		$data ['formName'] = $group ['form'];
		$data ['groups'] = $groups;
		$values = dbselect ( 'name,value' )->from ( '{preferences}' )->where ( array ('preference_group' => $_g ) )->toArray ( 'value', 'name' );
		$values ['site_url'] = isset ( $values ['site_url'] ) && ! empty ( $values ['site_url'] ) ? $values ['site_url'] : (BASE_URL == '/' ? '' : BASE_URL);
		$values ['debug_level'] = DEBUG;
		$data ['widgets'] = new DefaultFormRender ( $form->buildWidgets ( $values ) );
		return view ( 'preference.tpl', $data );
	}
	/**
	 * 保存通用设置.
	 *
	 * @return NuiAjaxView
	 */
	public function index_post($_g = 'core') {
		$groups = $this->bGroups;
		if (isset ( $groups [$_g] )) {
			$group = $groups [$_g];
		} else {
			$group = $this->defaultGroup;
			$_g = 'core';
		}
		if (is_subclass_of2 ( $group ['form'], 'AbstractForm' )) {
			$form = new $group ['form'] ();
		} else {
			$form = new DynamicForm ( $group ['form'], array (), true );
		}
		$cfgs = $form->valid ();
		if ($cfgs) {
			if ($_g == 'core') {
				$setting = KissGoSetting::getSetting ();
				if (! empty ( $cfgs ['site_url'] )) {
					$setting ['site_url'] = $cfgs ['site_url'];
				} else {
					unset ( $setting ['site_url'] );
				}
				$setting ['DEBUG'] = $cfgs ['debug_level'];
				$setting ['TIMEZONE'] = $cfgs ['timezone'];
				$setting->saveSettingToFile ( APPDATA_PATH . 'settings.php' );
			}
			$time = time ();
			$uid = $this->user->getUid ();
			$datas = array ();
			foreach ( $cfgs as $name => $value ) {
				$data = array ();
				$data ['preference_group'] = $_g;
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
			RtCache::delete ( 'system_preferences' );
			cfg ( '', '', true );
			return apply_filter ( 'on_preference_' . $_g . '_saved', NuiAjaxView::refresh ( "设置已保存." ) );
		} else {
			return NuiAjaxView::validate ( $group ['form'], '数据格式不正确，请重新填写.', $form->getErrors () );
		}
	}
	public function custom($group, $field = '') {
		$data ['cfg'] = $group;
		$widgets = new CustomeFieldWidgetRegister ();
		$data ['widgets'] = $widgets;
		$data ['type'] = 'text';
		if ($field) {
			$fields = dbselect ( 'value' )->from ( '{preferences}' )->where ( array ('name' => 'custom_fields','preference_group' => $group ) )->get ( 'value' );
			if ($fields) {
				$fields = @unserialize ( $fields );
			}
			if ($fields && isset ( $fields [$field] )) {
				$data = array_merge ( $data, $fields [$field] );
			}
		}
		$widget = $widgets->getWidget ( $data ['type'] );
		if ($widget) {
			$providor = $widget->getDataProvidor ( '' );
			$data ['defaultFormat'] = $providor->getOptionsFormat ();
		}
		$data ['field'] = $field;
		return view ( 'custom.tpl', $data );
	}
	public function custom_post() {
		$cform = new CustomCfgFieldForm ();
		$data = $cform->valid ();
		if ($data) {
			$cfg = $data ['cfg'];
			$field = $data ['name'];
			$fields = dbselect ( 'value' )->from ( '{preferences}' )->where ( array ('name' => 'custom_fields','preference_group' => $cfg ) )->get ( 'value' );
			if ($fields) {
				$fields = @unserialize ( $fields );
			}
			if (! $fields) {
				$fields = array ();
			}
			unset ( $data ['cfg'] );
			$fields [$field] = $data;
			set_cfg ( 'custom_fields', serialize ( $fields ), $cfg );
			return NuiAjaxView::refresh ( '自定义配置项已添加.' );
		} else {
			return NuiAjaxView::validate ( 'CustomCfgFieldForm', '表单检验失败', $cform->getErrors () );
		}
	}
	public function delf($group, $field) {
		if ($group && $field) {
			$fields = dbselect ( 'value' )->from ( '{preferences}' )->where ( array ('name' => 'custom_fields','preference_group' => $group ) )->get ( 'value' );
			if ($fields) {
				$fields = @unserialize ( $fields );
				if ($fields && isset ( $fields [$field] )) {
					unset ( $fields [$field] );
					set_cfg ( 'custom_fields', serialize ( $fields ), $group );
					dbdelete ()->from ( '{preferences}' )->where ( array ('name' => $field,'preference_group' => $group ) )->exec ();
					$cfgs = RtCache::get ( 'system_preferences', array () );
					if ($cfgs) {
						$key = $field . '@' . $group;
						unset ( $cfgs [$key] );
						RtCache::add ( 'system_preferences', $cfgs );
					}
				}
			}
		}
		return NuiAjaxView::refresh ( '自定义配置项已经删除.' );
	}
}
