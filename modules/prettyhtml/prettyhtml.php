<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );

bind ( 'on_load_dashboard_js_file', '&PrettyhtmlHookImpl' );
bind ( 'get_editor_plugins', '&PrettyhtmlHookImpl' );
bind ( 'get_editor_layout', '&PrettyhtmlHookImpl' );
bind ( 'on_load_dashboard_css', '&PrettyhtmlHookImpl' );
bind ( 'on_load_editor_css', '&PrettyhtmlHookImpl' );