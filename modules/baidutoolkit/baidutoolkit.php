<?php
defined('KISSGO') or exit ('No direct script access allowed');
bind('do_admin_layout', '&BaidutoolkitHookImpl');
bind('get_acl_resource', '&BaidutoolkitHookImpl');
bind('get_cms_preference_groups', '&BaidutoolkitHookImpl');
bind('get_activity_log_type', '&BaidutoolkitHookImpl');
bind('crontab', '&BaidutoolkitHookImpl', 1);
if (bcfg('enable_bd@bdtkit') && icfg('push_interval@bdtkit', 0) == -1) {
	bind('after_save_page', '&BaidutoolkitHookImpl');
	bind('after_page_published', '&BaidutoolkitHookImpl');
}
bind('on_init_pages_toolbar', '&BaidutoolkitHookImpl');