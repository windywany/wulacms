<?php
/**
 * 缓存设置控制器.
 *
 * @author ngf
 */
class MemcachedController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('index' => 'cache:system/preference','index_post' => 'cache:system/preference' );
	public function index() {
		$form = new DynamicForm ( 'MemPreferenceForm' );
		$data ['rules'] = $form->rules ();
		$data ['form'] = $form;
		$values = dbselect ( 'name,value' )->from ( '{preferences}' )->where ( array ('preference_group' => 'mem' ) )->toArray ( 'value', 'name' );
		
		if (! extension_loaded ( 'memcache' ) && ! extension_loaded ( 'memcached' )) {
			$values ['enable'] = '0';
			$data ['errorTip'] = 'memcache(d) 扩展未安装，本应用无法启用，请先安装memcache(d)扩展.';
		}
		$data ['widgets'] = new DefaultFormRender ( $form->buildWidgets ( $values ) );
		return view ( 'preference.tpl', $data );
	}
	public function index_post() {
		$form = new DynamicForm ( 'MemPreferenceForm' );
		$cfgs = $form->valid ();
		if ($cfgs) {
			$time = time ();
			$uid = $this->user->getUid ();
			$datas = array ();
			foreach ( $cfgs as $name => $value ) {
				$data = array ();
				$data ['preference_group'] = 'mem';
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
			return NuiAjaxView::refresh ( "设置已保存." );
		} else {
			return NuiAjaxView::validate ( 'MemPreferenceForm', '数据格式不正确，请重新填写.', $form->getErrors () );
		}
	}
	public function clear($type = 'all') {
		$prefix = rand_str ( 3 );
		$settings = KissGoSetting::getSetting ();
		$refresh = false;
		if ($type == 'all') {
			RtCache::clear ();
			rmdirs ( TMP_PATH . '#themes_c' );
			rmdirs ( TMP_PATH . '#tpls_c' );
			rmdirs ( TMP_PATH . 'cache' );
			fire ( 'on_clear_tpl_cache' );
			$settings ['cache_prefix'] = $prefix;
			$refresh = true;
		} else if ($type == 'block') {
			$settings ['block_cache_prefix'] = $prefix;
		} else if ($type == 'chunk') {
			$settings ['chunk_cache_prefix'] = $prefix;
		} else if ($type == 'page') {
			$settings ['page_cache_prefix'] = $prefix;
		} else if ($type == 'cnt') {
			$settings ['cnt_cache_prefix'] = $prefix;
		}
		$settings->saveSettingToFile ( APPDATA_PATH . 'settings.php' );
		return $refresh ? NuiAjaxView::refresh ( '缓存已经清空.' ) : NuiAjaxView::ok ( '缓存已经清空.' );
	}
}