<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );

bind ( 'do_admin_layout', '&AlbumHookImpl' );
bind ( 'load_album_model', '&AlbumHookImpl' );
bind ( 'on_load_dashboard_css', '&AlbumHookImpl' );
bind ( 'get_recycle_content_type', '&AlbumHookImpl' );
bind ( 'get_content_list_page_url', '&AlbumHookImpl', 100, 2 );
bind ( 'get_extra_saved_actions', '&AlbumHookImpl', 100, 2 );
bind ( 'on_init_rest_server', '&AlbumRestService' );
bind ( 'on_destroy_cms_page', '&AlbumHookImpl' );
bind ( 'on_destroy_album_item', '&AlbumHookImpl' );
bind ( 'on_load_page_fields', '&AlbumHookImpl' );