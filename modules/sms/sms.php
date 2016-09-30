<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
bind ( 'do_admin_layout', '&SmsHookImpl' );
bind ( 'get_acl_resource', '&SmsHookImpl' );
bind ( 'on_init_rest_server', '&sms\classes\SmsRestService' );