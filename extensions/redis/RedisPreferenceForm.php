<?php
namespace redis;

class RedisPreferenceForm extends \AbstractForm {

    private $host = array ('group' => '3','col' => '6','label' => 'Host','default' => 'localhost' );

    private $port = array ('group' => '3','col' => '2','label' => 'Port','default' => '6379','rules' => array ('range(1025,65530)' => '介于1025与65530之间' ) );

    private $db = array ('group' => '3','col' => '2','label' => 'DB','default' => '8','rules' => array ('range(0,15)' => '介于0与15之间' ) );

    private $auth = array ('group' => '3','col' => '2','label' => 'AUTH' );

    public static function get_preference_group($groups) {
        $groups ['redis'] = array ('name' => 'Redis配置','icon' => 'fa-inbox','form' => '\redis\RedisPreferenceForm' );
        return $groups;
    }

    public static function get_passport_setting_groups($groups) {
        $groups ['redis'] = 'Redis配置';
        return $groups;
    }
}