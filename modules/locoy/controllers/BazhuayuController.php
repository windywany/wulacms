<?php

class BazhuayuController extends NonSessionController {
	public function index($model) {
		$safe_code = rqst('pw');
		if ($safe_code != cfg('locoy_secret@locoy')) {
			Response::respond(401);
		}
		Request::getInstance()->addUserData(['model' => $model]);
		$allowed_models = cfg('allowed_models@locoy');

		if ($allowed_models && in_array($model, explode(',', $allowed_models))) {
			$content = trim(rqst('content'));
			if (empty ($content)) {
				Response::respond(403);
			}
			$rst = CmsPage::save('page', $model, null, false);
			if ($rst) {
				return 'page: ' . $rst;
			}
		}

		Response::respond(500);

		return 'error';
	}
}