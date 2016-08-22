<?php
class SmartAlert implements Renderable {
	private $alert;
	private function __construct($tpye = 'warning', $icon = 'fa-warning', $closable = true) {
		$this->alert = dashboard_htmltag ( 'div' )->cls ( 'alert alert-' . $tpye . ' fade in' );
		$this->icon = $icon;
		$this->closable = $closable;
		if ($closable) {
			$btn = dashboard_htmltag ( 'button' )->cls ( 'close' )->data_dismiss ( 'alert' )->text ( '×' );
			$this->alert->child ( $btn );
		}
		if ($icon) {
			$i = dashboard_htmltag ( 'i' )->cls ( 'fa-fw fa ' . $icon );
			$this->alert->child ( $i );
		}
	}
	/**
	 * 设置提示内容.
	 *
	 * @param string $message        	
	 */
	public function setMessage($message) {
		if ($message instanceof Renderable) {
			$this->alert->child ( $message );
		} else {
			$this->alert->text ( $message, true );
		}
	}
	public static function warning($message, $icon = 'fa-warning', $closable = true) {
		$alert = new SmartAlert ( 'warning', $icon, $closable );
		$alert->setMessage ( $message );
		return $alert;
	}
	public static function success($message, $icon = 'fa-check', $closable = true) {
		$alert = new SmartAlert ( 'success', $icon, $closable );
		$alert->setMessage ( $message );
		return $alert;
	}
	public static function info($message, $icon = 'fa-times', $closable = true) {
		$alert = new SmartAlert ( 'danger', $icon, $closable );
		$alert->setMessage ( $message );
		return $alert;
	}
	public static function error($message, $icon = 'fa-error', $closable = true) {
		$alert = new SmartAlert ( 'error', $icon, $closable );
		$alert->setMessage ( $message );
		return $alert;
	}
	public function render() {
		return $this->alert->render ();
	}
}