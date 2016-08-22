<?php
/*
 * 评论模块
 */
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
bind ( 'do_admin_layout', '&CommentHooksImpl', 3 );
bind ( 'get_acl_resource', '&CommentHooksImpl' );
bind ('get_recycle_content_type','&CommentHooksImpl' );