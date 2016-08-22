<?php
abstract class AbstractPassportController extends Controller {
	/**
	 *
	 * @var IPassportTheme
	 */
	protected $theme = null;
	public function preRun($method) {
		$this->setTheme ();
	}
	protected function setTheme($checkType = true) {
		if ($checkType) {
			$type = cfg ( 'type@passport', 'vip' );
			if ($type != 'vip') {
				Response::respond ( 404 );
			}
		}
		$theme = cfg ( 'layout@passport', 'UCHomeTheme' );
		if (class_exists ( $theme )) {
			$this->theme = new $theme ();
		}
		if (! $this->theme instanceof IPassportTheme) {
			$this->theme = new UCHomeTheme ();
		}
	}
}