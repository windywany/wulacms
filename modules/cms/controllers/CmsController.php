<?php
/*
 * KissCms
 */
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
/**
 * 管理员界面控制器.
 *
 * @author Guangfeng Ning <windywany@gmail.com>
 */
class CmsController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('cts' => 'tpl:cms','cts_post' => 'tpl:cms','preference' => 'cms:system/preference','preference_post' => 'cms:system/preference' );
	public function cts($provider = 'pages') {
		$data = array ();
		$data ['provider'] = $provider;
		$providers = KissGoSetting::getSetting ( 'cts_providers' );
		if ($providers) {
			foreach ( $providers as $key => $pro ) {
				list ( $p, $title, $desc, $con_func ) = $pro;
				if ($title) {
					$data ['providers'] [$key] = $title;
				}
			}
		}
		if (isset ( $providers [$provider] )) {
			list ( $f, $title, $desc, $con_func ) = $providers [$provider];
			if (is_array ( $f )) {
				list ( $func, $file ) = $f;
				if ($file) {
					include_once $file;
				}
			}
			$func = 'get_condition_for_' . $provider;
			$data ['widgets'] = array ();
			if (is_callable ( $func )) {
				$cons = call_user_func_array ( $func, array () );
				$data ['widgets'] = CustomeFieldWidgetRegister::initWidgets ( $cons );
			}
		}
		return view ( 'cts.tpl', $data );
	}
	public function cts_post($provider) {
		if (empty ( $provider )) {
			return NuiAjaxView::error ( '未指定数据源' );
		}
		$providers = KissGoSetting::getSetting ( 'cts_providers' );
		if (isset ( $providers [$provider] )) {
			list ( $f, $title, $desc, $con_func ) = $providers [$provider];
			if (is_array ( $f )) {
				list ( $func, $file ) = $f;
				if ($file) {
					include_once $file;
				}
			}
			$func = 'get_condition_for_' . $provider;
			$args = array ();
			$tplargs = array ();
			if (is_callable ( $func )) {
				$cons = call_user_func_array ( $func, array () );
				foreach ( $cons as $con ) {
					$val = rqst ( $con ['name'] );
					if (! empty ( $val ) || is_numeric ( $val )) {
						if (is_array ( $val )) {
							$val = implode ( ',', $val );
						}
						$args [$con ['name']] = $val;
						if (is_numeric ( $val )) {
							$tplargs [] = $con ['name'] . "={$val}";
						} else {
							$tplargs [] = $con ['name'] . "='{$val}'";
						}
					}
				}
			}
			$datas = get_data_from_cts_provider ( $provider, $args, array () );
			$mapfunc = 'get_fieldmap_for_' . $provider;
			$map = array ('id' => 'id','name' => 'title' );
			if (is_callable ( $mapfunc )) {
				$map = call_user_func_array ( $mapfunc, array () );
			}
			if (! is_array ( $map ) || ! isset ( $map ['id'] ) || ! isset ( $map ['name'] )) {
				$map = array ('id' => 'id','name' => 'title' );
			}
			$trs = array ();
			$mapId = $map ['id'];
			$mapName = $map ['name'];
			$fields = array ();
			if ($datas->total ()) {
				$fields = $datas->getData ();
				foreach ( $datas as $data ) {
					$id = $data [$mapId];
					$name = $data [$mapName];
					$trs [] = "<tr><td>{$id}</td><td>{$name}</td></tr>";
				}
			} else {
				$trs [] = '<tr><td colspan="2">未调取到数据</td></tr>';
			}
			$tpl = '{cts var=name from=' . $provider . ' ' . implode ( ' ', $tplargs ) . '}{/cts}';
			
			$args = array ('fields' => var_export ( $fields, true ),'cts' => $tpl,'data' => implode ( '', $trs ),'ct' => $datas->getCountTotal () );
			return NuiAjaxView::callback ( 'setPreviewData', $args );
		} else {
			return NuiAjaxView::error ( '未注册的数据源：' . $provider );
		}
	}
	public function preference($_g = 'base') {
		$groups = $this->getPGroups ();
		if ($_g == 'base' || ! isset ( $groups [$_g] )) {
			$form = new DynamicForm ( 'AdminPreferenceForm' );
			$data ['rules'] = $form->rules ();
			$data ['form'] = $form;
			$data ['_g'] = 'base';
			$values = dbselect ( 'name,value' )->from ( '{preferences}' )->where ( array ('preference_group' => 'cms' ) )->toArray ( 'value', 'name' );
			$data ['widgets'] = new DefaultFormRender ( $form->buildWidgets ( $values ) );
		} else {
			$g = $groups [$_g];
			$formCls = $g ['form'];
			$form = new $formCls ();
			$data ['rules'] = $form->rules ();
			$data ['form'] = $form;
			$data ['_g'] = $_g;
			$pg = isset ( $g ['group'] ) && $g ['group'] ? $g ['group'] : 'cms';
			$values = dbselect ( 'name,value' )->from ( '{preferences}' )->where ( array ('preference_group' => $pg ) )->toArray ( 'value', 'name' );
			$data ['widgets'] = new DefaultFormRender ( $form->buildWidgets ( $values ) );
		}
		$data ['groups'] = $groups;
		return view ( 'preference.tpl', $data );
	}
	public function preference_post($_g = 'base') {
		$groups = $this->getPGroups ();
		if ($_g == 'base' || ! isset ( $groups [$_g] )) {
			$form = new DynamicForm ( 'AdminPreferenceForm' );
			$pg = 'cms';
		} else {
			$g = $groups [$_g];
			$formCls = $g ['form'];
			$form = new $formCls ();
			$pg = isset ( $g ['group'] ) && $g ['group'] ? $g ['group'] : 'cms';
		}
		$cfgs = $form->valid ();
		if ($cfgs) {
			$time = time ();
			$uid = $this->user->getUid ();
			$datas = array ();
			foreach ( $cfgs as $name => $value ) {
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
			RtCache::delete ( 'system_preferences' );
			cfg ( '', '', true );
			return apply_filter ( 'on_preference_' . $pg . '_saved', NuiAjaxView::ok ( "设置保存完成." ) );
		} else {
			return NuiAjaxView::validate ( 'AdminPreferenceForm', '数据格式不正确，请重新填写.', $form->getErrors () );
		}
	}
	private function getPGroups() {
		$groups = apply_filter ( 'get_cms_preference_groups', array ('base' => array ('icon' => 'fa-cog','name' => '基本','form' => 'AdminPreferenceForm','group' => 'cms' ) ) );
		return $groups;
	}
}