<?php
/**
 * 内链表单.
 *
 * @author Guangfeng
 */
class TagForm extends AbstractForm {
	private $id = array ('rules' => array ('regexp(/^[0-9]+$/)' => '非法的编号.' ) );
	private $tag = array ('rules' => array ('required' => '标签不能为空.' ) );
	private $title;
	private $url = array ('rules' => array ('required' => 'URL不能为空.' ) );
	
	/**
	 * 替换$content中的关键词为内联库.
	 *
	 * @param string $content        	
	 * @return string
	 */
	public static function applyTags($content) {
		if ($content) {
			$tmp = preg_replace ( '#<[^>]+>#', '', $content );
			$tmp = preg_replace ( '#&.+?;#', '', $tmp );
			$rcount = intval ( cfg ( 'tags_count@cms', 50 ) );
			if (! $rcount) {
				$rcount = 50;
			}
			$tags = get_keywords ( null, $tmp, $rcount, TagForm::getDictFile () );
			list ( $tag, $_ ) = $tags;
			if ($tag) {
				$tags = explode ( ',', $tag );
				$inline = dbselect ( 'url,title,tag' )->from ( '{cms_tag}' )->where ( array ('tag IN' => $tags,'deleted' => 0 ) );
				$tags = array ();
				foreach ( $inline as $tag ) {
					$len = strlen ( $tag ['tag'] );
					$tags [$len] [] = $tag;
				}
				if ($tags) {
					krsort ( $tags );
					$count = intval ( cfg ( 'tag_count@cms', 0 ) );
					if (! $count) {
						$count = - 1;
					}
					foreach ( $tags as $tag ) {
						foreach ( $tag as $t ) {
							if (! in_atag ( $content, $t ['tag'] )) {
								$search = '`' . preg_quote ( $t ['tag'], '`' ) . '`u';
								$url = safe_url ( $t ['url'], true );
								$content = preg_replace ( $search, '<a href="' . $url . '" title="' . $t ['title'] . '">' . $t ['tag'] . '</a>', $content, $count );
							}
						}
					}
				}
			}
		}
		return $content;
	}
	public static function getDictFile() {
		$file = TMP_PATH . 'tag_dict.xdb';
		return file_exists ( $file ) ? $file : false;
	}
	public static function generateScwsDictFile() {
		$update = true;
		$file = TMP_PATH . 'tag_dict.xdb';
		if (file_exists ( $file )) {
			$maxTime = dbselect ()->field ( imv ( 'MAX(update_time)', 'ut' ) )->from ( '{cms_tag}' )->get ();
			$maxTime = $maxTime ['ut'];
			$f = filemtime ( $file );
			$update = $f < $maxTime;
		}
		
		if ($update) {
			$tags = dbselect ( 'tag' )->from ( '{cms_tag}' )->where ( array ('deleted' => 0 ) );
			$tags = $tags->toArray ( 'tag' );
			$xdb = new XTreeDB ();
			if ($tags && $xdb->Open ( $file, 'w' )) {
				$total = 0;
				$rec = array ();
				foreach ( $tags as $word ) {
					if (strlen ( $word ) < 2) {
						continue;
					}
					$k = (ord ( $word [0] ) + ord ( $word [1] )) & 0x3f;
					if (! isset ( $rec [$k] )) {
						$rec [$k] = array ();
					}
					if (! isset ( $rec [$k] [$word] )) {
						$total ++;
						$rec [$k] [$word] = array ();
					}
					$rec [$k] [$word] ['tf'] = '1.0';
					$rec [$k] [$word] ['idf'] = '1.0';
					$rec [$k] [$word] ['attr'] = 'nk';
					$len = mb_strlen ( $word );
					while ( $len > 2 ) {
						$len --;
						$temp = mb_substr ( $word, 0, $len );
						if (! isset ( $rec [$k] [$temp] )) {
							$total ++;
							$rec [$k] [$temp] = array ();
						}
						$rec [$k] [$temp] ['part'] = 1;
					}
				}
				
				for($k = 0; $k < 0x40; $k ++) {
					if (! isset ( $rec [$k] )) {
						continue;
					}
					$cnt = 0;
					foreach ( $rec [$k] as $w => $v ) {
						$flag = (isset ( $v ['tf'] ) ? 0x01 : 0);
						if (isset ( $v ['part'] ) && $v ['part']) {
							$flag |= 0x02;
						}
						$data = pack ( 'ffCa3', $v ['tf'], $v ['idf'], $flag, $v ['attr'] );
						$xdb->Put ( $w, $data );
						$cnt ++;
					}
				}
				
				$xdb->Optimize ();
				$xdb->Close ();
				if (file_exists ( $file )) {
					@chmod ( $file, 0666 );
				}
			} else {
				log_warn ( 'cannot open ' . $file );
			}
		}
	}
}
