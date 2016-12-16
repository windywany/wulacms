<?php
/**
 * Created by PhpStorm.
 * DEC :
 * User: wangwei
 * Date: 2016/12/2
 * Time: 10:19
 */

namespace taoke\classes;

class PreferenceForm extends \AbstractForm {
	protected $__cfg_group = 'taoke';

	private $appkey    = array('group' => '2', 'col' => '4', 'label' => 'appkey', 'default' => '',);
	private $appsecret = array('group' => '2', 'col' => '4', 'label' => 'appsecret', 'default' => '');
	private $user_id   = array('group' => '2', 'col' => '4', 'label' => 'user_id', 'default' => '');
}