<?php
/*
 * 评论模块
 */
defined('KISSGO') or exit ('No direct script access allowed');
bind('do_admin_layout', '&CommentHooksImpl', 3);
bind('get_acl_resource', '&CommentHooksImpl');
bind('get_recycle_content_type', '&CommentHooksImpl');
bind('mobiapp_page_query', '&CommentHooksImpl');
bind('on_init_rest_server', '&CommentHooksImpl');