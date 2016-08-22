<?php
/**
 * digg controller.
 * @author ngf
 *
 */
class DiggController extends NonSessionController {
	public function index() {
		$uuid = Request::getUUID ();
		$id = irqst ( 'id' );
		$digg = irqst ( 'digg' );
		$rst = DiggRestService::digg ( $uuid, $id, $digg );
		if ($rst === true) {
			return new JsonView ( array ('success' => true ) );
		} else {
			return new JsonView ( array ('success' => false,'msg' => $rst ) );
		}
	}
}