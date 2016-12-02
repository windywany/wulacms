<?php
defined('KISSGO') or exit ('No direct script access allowed');

class PreferenceController extends DefaultPreferencePage {
	protected $checkUser = true;
	protected $acls      = array('index' => 'preference:taoke/setting', 'index_post' => 'preference:taoke/setting');

	protected function getPreferenceGroup($type) {
		return 'taoke';
	}

	protected function getCurrentURL() {
		return 'taoke/preference';
	}

	protected function getForm($type, $data = []) {

			return new \taoke\classes\PreferenceForm();

	}

	protected function icando($user) {
		return icando('preference:taoke', $user);
	}


	public function index_post($_g = 'base', $_hp = '') {

		if (!$this->icando($this->user)) {
			Response::showErrorMsg('你无权进行' . $this->getTitle());
		}
		$data ['groups'] = $this->getGroups();
		if (!isset ($data ['groups'] [ $_g ])) {
			Response::respond(404);
		}
		$form = $this->getForm($_g);

		$cfgs = $form->valid();
		if ($cfgs) {
			$data ['customEnabled'] = $this->supportCustomField();
			$cfields                = array();
			if ($data ['customEnabled']) {
				$cfields = dbselect('value')->from('{preferences}')->where(array('name' => 'custom_fields', 'preference_group' => $data ['customEnabled']))->get('value');
				if ($cfields) {
					$cfields = @unserialize($cfields);
				}
				if ($cfields === false) {
					$cfields = array();
				}
			}

			$time  = time();
			$uid   = $this->user->getUid();
			$datas = array();
			$pg    = $this->getPreferenceGroup($_g);
			$cfgs  = apply_filter('before_save_preference_' . $pg, $cfgs);
			foreach ($cfgs as $name => $value) {
				if (isset ($cfields [ $name ])) {
					$hook = 'alter_' . $cfields [ $name ] ['type'] . '_field_value';
					if (has_hook($hook)) {
						$value = apply_filter($hook, $value, $name);
					}
				}
				$data                      = array();
				$data ['preference_group'] = $pg;
				$data ['name']             = $name;
				$cfg                       = dbselect('preference_id,value')->from('{preferences}')->where($data)->get();
				if ($cfg && $cfg ['value'] != $value) {
					// updata
					$data ['value']       = is_array($value) ? implode(',', $value) : $value;
					$data ['update_time'] = $time;
					$data ['user_id']     = $uid;
					unset ($cfg ['value']);
					dbupdate('{preferences}')->set($data)->where($cfg)->exec();
				} else if (!$cfg) {
					$data ['value'] = is_array($value) ? implode(',', $value) : $value;

					$data ['update_time'] = $time;
					$data ['user_id']     = $uid;
					$datas []             = $data;
				}
			}
			if ($datas) {
				dbinsert($datas, true)->into('{preferences}')->exec();
			}

			$rtn = apply_filter('on_preference_' . $pg . '_saved', NuiAjaxView::refresh("设置已保存."), $cfgs);
			RtCache::delete('system_preferences');
			cfg('', '', true);

			return $rtn;
		} else {
			return NuiAjaxView::validate(get_class($form), '数据格式不正确，请重新填写.', $form->getErrors());
		}
	}

	protected function getTitle() {
		return '淘宝客设置';
	}

//	protected function getGroups() {
//		$groups = array(
//			'taoke' => new PreferenceConfig ('大神基础设置', 'dashen', new \taoke\classes\PreferenceForm ()),
////			'dashen_cfg' => new PreferenceConfig ('其他配置', 'dashen', new \dashen\forms\ConfigForm ()),
////			'gearman_cfg' => new PreferenceConfig ('GEARMAN配置', 'dashen', new \dashen\forms\GearmanForm()),
////			'huanxin_cfg' => new PreferenceConfig ('环信配置', 'dashen', new \dashen\forms\HuanxinForm()),
////			'mem_exp_cfg' => new PreferenceConfig ('算法配置', 'dashen', new \dashen\forms\MemberExpressionForm())
//		);
//
	//		return $groups;
	//	}

}