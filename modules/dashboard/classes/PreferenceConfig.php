<?php
class PreferenceConfig {
	private $name;
	private $form;
	private $group;
	public function __construct($name, $group, $form) {
		$this->group = $group;
		$this->name = $name;
		$this->form = $form;
	}
	public function getPreferenceGroup() {
		return $this->group;
	}
	public function getForm() {
		return $this->form;
	}
	public function __toString() {
		return $this->name;
	}
}