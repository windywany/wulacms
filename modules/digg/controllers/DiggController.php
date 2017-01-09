<?php

/**
 * digg controller.
 * @author ngf
 *
 */
class DiggController extends NonSessionController {
	public function index() {
		$uuid = Request::getUUID();
		if (empty($uuid)) {
			return array('success' => true, 'digg' => array());
		}
		$id   = irqst('id');
		$digg = irqst('digg');
		$rst  = DiggRestService::digg($uuid, $id, $digg);
		if (is_array($rst)) {
			return new JsonView (array('success' => true, 'digg' => $rst));
		} else {
			return new JsonView (array('success' => false, 'msg' => $rst));
		}
	}

	public function get($id) {
		$rst = DiggRestService::getPageDigg($id);

		return ['digg' => $rst];
	}
}