<?php
defined('KISSGO') or exit ('No direct script access allowed');

bind('do_admin_layout', '&taoke\classes\TaokeHookImpl');
bind('get_content_list_page_url', '&taoke\classes\TaokeHookImpl', 10, 2);
bind('load_taoke_model', '&taoke\classes\TaokeHookImpl');
bind('on_destroy_cms_page', '&taoke\classes\TaokeHookImpl');
bind('get_artisan_commands', '&taoke\classes\ImportGoodsFromExcelCommand');
bind('build_page_common_query', '&taoke\classes\TaokeHookImpl', 1, 2);
bind('get_columns_of_tbkGoodsTable', '&taoke\classes\TaokeHookImpl');
if (extension_loaded('scws')) {
	bind('after_save_page_taoke', '&taoke\classes\TaokeHookImpl');
}
bind('weixin_auto_reply_text_message', '&taoke\classes\TaokeHookImpl', 1, 3);
bind('weixin_auto_reply_voice_message', '&taoke\classes\TaokeHookImpl', 1, 3);
