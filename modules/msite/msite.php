<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
define ( 'ENABLE_SUB_DOMAIN', true );
bind ( 'do_admin_layout', '&MSiteHooks' );
bind ( 'get_acl_resource', '&MSiteHooks' );
bind ( 'filter_data_for_safe_url', '&MSiteHooks', 0 );
bind ( 'get_mobile_url', '&MSiteHooks', 0, 2 );
bind ( 'on_render_homepage', '&MSiteHooks' );
bind ( 'after_save_channel', '&MSiteHooks' );
bind ( 'on_render_page', '&MSiteHooks' );
