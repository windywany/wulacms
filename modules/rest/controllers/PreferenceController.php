<?php
/**
 * 应用中心设置.
 * @author Guangfeng
 *
 */
class PreferenceController extends Controller {
	protected $checkUser = true;
	protected $acls = array ('index' => 'rest:system/preference','index_post' => 'rest:system/preference' );
	public function index($_g = 'base') {
		$_g = 'base';
		$form = new RestPreferenceForm ();
		$data ['_g'] = $_g;
		$data ['rules'] = $form->rules ();
		$data ['form'] = $form;
		$data ['groups'] = array ('base' => '基本设置' );
		$data ['formName'] = get_class ( $form );
		$values = dbselect ( 'name,value' )->from ( '{preferences}' )->where ( array ('preference_group' => 'rest' ) )->toArray ( 'value', 'name' );
		$data ['widgets'] = new DefaultFormRender ( $form->buildWidgets ( $values ) );
		return view ( 'preference.tpl', $data );
	}
	public function index_post($_g = 'base') {
		$_g = 'base';
		$form = new RestPreferenceForm ();
		$cfgs = $form->valid ();
		if ($cfgs) {
			$time = time ();
			$uid = $this->user->getUid ();
			$datas = array ();
			foreach ( $cfgs as $name => $value ) {
				$data = array ();
				$data ['preference_group'] = 'rest';
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
			return NuiAjaxView::validate ( get_class ( $form ), '数据格式不正确，请重新填写.',$form->getErrors() );
		}
	}
}