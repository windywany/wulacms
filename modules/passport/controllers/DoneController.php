<?php

namespace passport\controllers;

class DoneController extends \Controller {
	public function index($uid) {
		$uid = intval($uid);
		if (empty ($uid)) {
			Response::redirect(tourl('passport/join'));
		}
		$user = dbselect('*')->from('{member}')->where(array('mid' => $uid))->get(0);
		if (!$user) {
			Response::redirect(tourl('passport/join'));
		}
		$user              = apply_filter("load_member_data", $user);
		$data ['user']     = $user;
		$data ['join_url'] = cfg('join_url@passport', DETECTED_ABS_URL);

		return view($this->theme->done(), $data);
	}
}