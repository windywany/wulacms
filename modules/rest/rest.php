<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );

bind ( 'do_admin_layout', 'hook_for_do_admin_layout_rest@hooks/do_admin_layout' );
bind ( 'get_activity_log_type', 'hook_for_activity_types_rest@hooks/do_admin_layout' );
bind ( 'get_acl_resource', 'filter_for_rest_acl_resource@hooks/get_acl_resource' );
bind ( 'crontab', 'hook_for_rest_crontab@hooks/rest_cron_hook' );