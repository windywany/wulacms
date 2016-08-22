<?php
/*
 * 顶踩模块(评分,有无帮助等功能) 本模块支持10级评分,网站可以根据自己的需求定义每一级代表的涵意.如: 0代表有帮助; 1代表无帮助 0代表顶; 1代表踩 1代表1星评分,2代表2星评分等.
 */
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );

// bind('render_cms_data_report_tabs','&DiggHooksImpl');
// bind ('render_cms_data_report_charts','&DiggHooksImpl');

bind ( 'load_page_common_fields', '&DiggHooksImpl' );
bind ( 'save_page_common_data', '&DiggHooksImpl' );
bind ( 'build_page_common_query', '&DiggHooksImpl' );
bind ( 'get_cms_preference_groups', '&DiggHooksImpl' );
bind ( 'load_page_common_data', '&DiggHooksImpl' );
bind ( 'on_init_rest_server', '&DiggHooksImpl' );
bind ( 'show_page_detail', '&DiggHooksImpl', 100, 2 );
bind ( 'crontab', '&DiggHooksImpl' );
