<?php

namespace passport\controllers;

use passport\models\MemberModel;

class DoneController extends \Controller {
	public function index($uid) {
		$uid = intval($uid);
		if (empty ($uid)) {
			\Response::redirect(tourl('passport/join'));
		}
		$model = new MemberModel();
		$user  = $model->get($uid);
		if (!$user) {
			\Response::redirect(tourl('passport/join'));
		}
		$s = $user['status'];
		if (in_array($s, ['2', '3'])) {
			\Response::redirect(tourl('passport/active/' . $uid));
		}
		$user                  = apply_filter("load_member_data", $user);
		$data ['user']         = $user;
		$data ['join_url']     = tourl('passport/join');
		$data ['redirect_url'] = cfg('join_url@passport', '/');
		$data ['login_url']    = tourl('passport') . '?from=' . urlencode(cfg('redirect_url@passport', '/'));

		return template('passport/done.tpl', $data);
	}
}