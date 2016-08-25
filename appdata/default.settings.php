<?php
/*
 * kissgo framework that keep it simple and stupid, go go go ~~ default configuration
 */
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
$settings = KissGoSetting::getSetting ( 'install' );
// //////////////////////////////////////////////////////////////////////
// built in apps
// //////////////////////////////////////////////////////////////////////
KissGoSetting::addBuiltinApp ( 'system' );
KissGoSetting::addBuiltinApp ( 'dashboard' );
KissGoSetting::addBuiltinApp ( 'rest' );
KissGoSetting::addBuiltinApp ( 'media' );
KissGoSetting::addBuiltinApp ( 'cms' );
KissGoSetting::addBuiltinApp ( 'prettyhtml' );
// //////////////////////////////////////////////////////////////////////
// you can add some settings below,
// these settings will not be overrided while we are installing kissgo.
// //////////////////////////////////////////////////////////////////////
// $settings ['BASE_URL'] = '/';
// $settings ['your_setting1'] = 'setting value';
// $settings ['your_setting2'] = 'setting value';

// end of default.settings.php
?>