<?php

class PreferenceConfig {
	public $name;
	public $form;
	public $group;
	public $icon;

	public function __construct($name, $group, $form, $icon = '') {
		$this->group = $group;
		$this->name  = $name;
		$this->form  = $form;
		$this->icon  = $icon;
	}

	public function getPreferenceGroup() {
		return $this->group;
	}

	public function getForm() {
		return $this->form;
	}

	public function getIcon() {
		return $this->icon;
	}

	public function __toString() {
		return $this->name;
	}
}