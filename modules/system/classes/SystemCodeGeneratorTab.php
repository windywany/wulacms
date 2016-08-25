<?php
class SystemCodeGeneratorTab extends SmartTab {
	private $activedTab;
	public function __construct($actived) {
		parent::__construct ( 'system-code-gen-tab' );
		$this->activedTab = $actived;
		$this->initTabs ();
	}
	private function initTabs() {
		$this->add ( '模块生成器', '', true );
		$this->add ( '控制器生成器', '' );
	}
}