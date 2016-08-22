<?php
class DiggHooksImpl {
	public static function load_page_common_fields($fields) {
		for($i = 0; $i < 10; $i ++) {
			if (bcfg ( 'digg' . $i . '_enabled@digg' )) {
				$fields [] = array ('group' => 'digg','col' => '1','name' => 'digg_' . $i,'label' => cfg ( 'digg' . $i . '_name@digg', 'digg' . $i ),'rules' => array ('regexp(/^\d+$/)' => '数字.' ) );
			}
		}
		return $fields;
	}
	public static function load_page_common_data($values) {
		$page_id = $values ['page_id'];
		if ($page_id) {
			$diggs = dbselect ( '*' )->from ( '{cms_digg}' )->where ( array ('page_id' => $page_id ) )->get ( 0 );
			if ($diggs) {
				$values = array_merge ( $values, $diggs );
			}
		}
		return $values;
	}
	public static function save_page_common_data($page) {
		$data ['page_id'] = $page ['page_id'];
		$data ['digg_total'] = 0;
		for($i = 0; $i < 10; $i ++) {
			$f = 'digg_' . $i;
			if (bcfg ( 'digg' . $i . '_enabled@digg' )) {
				$data [$f] = irqst ( $f, 0 );
				$data ['digg_total'] += $data [$f];
			} else {
				$data [$f] = 0;
			}
		}
		dbsave ( $data, array ('page_id' => $page ['page_id'] ), 'page_id' )->into ( '{cms_digg}' )->exec ();
	}
	/**
	 *
	 * @param Query $query        	
	 */
	public static function build_page_common_query($query) {
		if ($query) {
			$query->field ( 'DIGG.digg_total' );
			for($i = 0; $i < 10; $i ++) {
				if (bcfg ( 'digg' . $i . '_enabled@digg' )) {
					$query->field ( 'DIGG.digg_' . $i );
				}
			}
			$query->join ( '{cms_digg} AS DIGG', 'CP.id = DIGG.page_id' );
		}
		return $query;
	}
	public static function show_page_detail($html, $page) {
		$htmls = array ();
		for($i = 0; $i < 10; $i ++) {
			if (isset ( $page ['digg_' . $i] )) {
				$htmls [] = cfg ( 'digg' . $i . '_name@digg', 'digg' . $i ) . ':' . intval ( $page ['digg_' . $i] );
			}
		}
		if ($htmls) {
			$html .= '<p>';
			$html .= implode ( ',', $htmls );
			$html .= '</p>';
		}
		return $html;
	}
	public static function get_cms_preference_groups($groups) {
		$groups ['digg'] = array ('name' => '顶','form' => 'DiggPreferenceForm','group' => 'digg','icon' => 'fa-digg' );
		return $groups;
	}
	public static function crontab($time) {
		$time = strtotime ( '-1 years' );
		dbdelete ()->from ( '{cms_digg_log}' )->where ( array ('create_time <' => $time ) )->exec ();
	}
	/**
	 *
	 * @param RestServer $server        	
	 */
	public static function on_init_rest_server($server) {
		$server->registerClass ( new DiggRestService (), '1', 'digg' );
		return $server;
	}
}