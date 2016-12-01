<?php

class PreferenceController extends DefaultPreferencePage {
	protected function getPreferenceGroup($type) {
		$groups = $this->getGroups();
		if (isset ($groups [ $type ])) {
			$cfg = $groups [ $type ];

			return $cfg->getPreferenceGroup();
		}

		return new WeixinPreferencesForm ();
	}

	protected function getCurrentURL() {
		return tourl('weixin/preference', false);
	}

	protected function getForm($type, $data = array()) {
		$groups = $this->getGroups();
		if (isset ($groups [ $type ])) {
			$cfg = $groups [ $type ];

			return $cfg->getForm();
		}

		return new WeixinPreferencesForm ();
	}

	protected function icando($user) {
		return icando('weixin:system/preference', $user);
	}

	/**
	 * 重写父类方法，保存数组
	 *
	 * @author DQ
	 * @date   2016年2月15日 下午5:30:10
	 *
	 * @param
	 *
	 * @return
	 *
	 */
	public function index_post($_g = 'base') {
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
					//updata
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
		return '微信接口设置';
	}

	protected function getGroups() {
		$groups = apply_filter('get_weixin_preference_group', array('base' => new PreferenceConfig ('接入设置', 'weixin', new WeixinPreferencesForm ()), 'share' => new PreferenceConfig ('分享设置', 'weixin', new WeixinSharePreferencesForm())));

		return $groups;
	}
}