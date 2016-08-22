<?php
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );
function page_checkbox_options_by_media($content) {
	if (bcfg ( 'enable_download@media' )) {
		$content .= '<label class="checkbox"><input type="checkbox" name="download_remote_pic" checked="checked"/><i></i>下载远程图片</label>';
	}
	return $content;
}
function before_save_page_by_media($page) {
	if (rqst ( 'download_remote_pic' ) == 'on' && bcfg ( 'enable_download@media' ) && $page ['content']) {
		$content = $page ['content'];
		if (preg_match_all ( '#<img.+?src\s*=\s*["\'](https?[^"\']+?)["\'].*?>#im', $content, $ms )) {
			$uploader = bcfg ( 'store_type@media' ) ? new RemoteUploader () : apply_filter ( 'get_uploader', new FileUploader () );
			$exclude_urls = explode ( "\n", cfg ( 'exclude_url@media' ) );
			foreach ( $exclude_urls as $id => $url ) {
				$exclude_urls [$id] = trim ( $url, "\r" );
			}
			$images = array ();
			$mosaic = null;
			foreach ( $ms [1] as $idx => $m ) {
				$info = parse_url ( $m, PHP_URL_HOST );
				if ($info && ! in_array ( $info, $exclude_urls )) {
					if (preg_match ( '/\{\{pos:([^,]*?),w:([^,]*?),h:([^,]*?)\}\}/i', $ms [0] [$idx], $mmm )) {
						if ($mmm [1] && $mmm [2] && $mmm [3]) {
							$mosaic = array ($mmm [1],$mmm [2] . 'x' . $mmm [3] );
							$content = str_replace ( $mmm [0], '', $content );
						}
					}
					$images [$m] = array ($m,$mosaic );
				}
			}
			$watermark = cfg ( 'watermark@media' );
			if ($watermark && file_exists ( WEB_ROOT . $watermark )) {
				$watermarkcfg = array (WEB_ROOT . $watermark,cfg ( 'watermark_pos@media', 'br' ),cfg ( 'watermark_min_size@media' ) );
			} else {
				$watermarkcfg = false;
			}
			// log_debug ( 'to be downloaded images count:' . count ( $images ) );
			$rst = ImageUtil::downloadRemotePic ( $images, $uploader, cfg ( 'timeout@media', 30 ), $watermarkcfg );
			foreach ( $rst as $old => $new ) {
				$content = str_replace ( $old, the_media_src ( $new [0] ), $content );
			}
			$page ['content'] = $content;
		}
	}
	return $page;
}
function hook_for_media_after_load_page_fields($fields) {
	if (bcfg ( 'title_alt@media' )) {
		$title = $fields ['title'] ? $fields ['title'] : $fields ['title2'];
		if (isset ( $fields ['content'] ) && $fields ['content'] && $title) {
			if (preg_match_all ( '#<img[^>]+?>#ims', $fields ['content'], $ms, PREG_SET_ORDER )) {
				foreach ( $ms as $m ) {
					$chunks = preg_split ( '/\s+/', $m [0] );
					$hasAlt = false;
					foreach ( $chunks as $key => $c ) {
						if (preg_match ( '#alt\s*=\s*[\'"]?[^\s\'"]+?[\'"]?#i', $c )) {
							$hasAlt = true;
							break;
						} else if (preg_match ( '#alt\s*=\s*[\'"][\'"]?#i', $c )) {
							$chunks [$key] = '';
						}
					}
					
					if (! $hasAlt) {
						
						$chunk = array_pop ( $chunks );
						$chunks [] = 'alt="' . html_escape ( $title ) . '"';
						$chunks [] = $chunk;
						$chunks = implode ( ' ', $chunks );
						$fields ['content'] = str_replace ( $m [0], $chunks, $fields ['content'] );
					}
				}
			}
		}
	}
	return $fields;
}

