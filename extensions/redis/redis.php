<?php
/**
 * redis扩展
 */
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );

bind ( 'get_preference_group', '&redis\RedisPreferenceForm' );
//bind ( 'get_passport_setting_groups', '&redis\RedisPreferenceForm' );

