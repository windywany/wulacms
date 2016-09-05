<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
bind ( 'do_admin_layout', '&SmsHookImpl' );
bind ( 'get_acl_resource', '&SmsHookImpl' );
bind ( 'get_sms_templates', '&sms\classes\RegCodeTemplate' );
bind ( 'on_init_rest_server', '&sms\classes\SmsRestService' );