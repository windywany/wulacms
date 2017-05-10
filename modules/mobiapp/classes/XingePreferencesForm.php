<?php
class XingePreferencesForm extends AbstractForm {
	private $xinge_app_id = array ('label' => 'APP ID','rules' => array ('required' => '请填写APP ID'));
	private $xinge_secret_key = array ('label' => 'secret key','rules' => array ('required' => '请填写secret key' ) );
}