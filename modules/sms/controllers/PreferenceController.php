<?php

namespace sms\controllers;

use sms\classes\SmsPreferencesForm;

class PreferenceController extends \DefaultPreferencePage {
	protected function getForm($type, $data = []) {
		return new SmsPreferencesForm ($data);
	}

	protected function getCurrentURL() {
		return 'sms/preference';
	}

	protected function getPreferenceGroup($type) {
		return 'sms';
	}

	/*
	 * (non-PHPdoc) @see DefaultPreferencePage::getTitle()
	 */
	protected function getTitle() {
		return '短信通道配置';
	}

	protected function icando($user) {
		return icando('sms:system/preference', $user);
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see DefaultPreferencePage::getTemplate()
	 */
	protected function getTemplate($type) {
		return 'preference.tpl';
	}
}