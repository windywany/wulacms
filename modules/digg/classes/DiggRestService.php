<?php
/**
 * 允许通过RestFul方式digg一篇文章.
 * @author leo
 *
 */
class DiggRestService {
	public function rest_digg($param, $key = '', $secret = '') {
		if (isset ( $param ['id'] ) && isset ( $param ['digg'] ) && isset ( $param ['uuid'] )) {
			$page_id = intval ( $param ['id'] );
			$digg = intval ( $param ['digg'] );
			$uuid = $param ['uuid'];
			$rst = DiggRestService::digg ( $uuid, $page_id, $digg );
			if (true === $rst) {
				return array ('error' => 0 );
			} else {
				return array ('error' => 2,'message' => $rst );
			}
		}
		return array ('error' => 1,'message' => '参数不正确，参数必须包括id,digg和用户的uuid' );
	}
	/**
	 * digg a page.
	 *
	 * @param string $uuid
	 *        	用户唯一标识（最大长度不超过13个字符串，可以是用户ID）.
	 * @param int $page_id
	 *        	页面ID.
	 * @param int $digg
	 *        	digg 值,正值增加，负值减去.
	 * @return boolean|string 如果digg成功，则返回true,否则返回错误信息.
	 */
	public static function digg($uuid, $page_id, $digg) {
		$r_digg = abs ( $digg );
		if ($r_digg >= 0 && $r_digg < 10 && bcfg ( 'digg' . $r_digg . '_enabled@digg' )) {
			$log ['page_id'] = $page_id;
			$log ['uuid'] = $uuid;
			$log ['digg'] = strval ( $r_digg );
			if ($digg < 0 && dbselect ()->from ( '{cms_digg_log}' )->where ( $log )->exist ( 'digg' )) {
				$data ['digg_total'] = imv ( 'digg_total - 1' );
				$diggv = 'digg_' . $r_digg;
				$data [$diggv] = imv ( $diggv . ' - 1' );
				dbupdate ( '{cms_digg}' )->set ( $data )->where ( array ('page_id' => $page_id ) )->exec ();
				return true;
			} else if ($digg < 0) {
				return '你无权进行此操作';
			} else {
				$log ['create_time'] = time ();
				if (dbinsert ( $log )->into ( '{cms_digg_log}' )->exec () !== false) {
					if (dbselect ()->from ( '{cms_page}' )->where ( array ('id' => $page_id ) )->exist ( 'id' )) {
						$data ['digg_total'] = imv ( 'digg_total+1' );
						$diggv = 'digg_' . $r_digg;
						$data [$diggv] = imv ( $diggv . '+1' );
						if (! dbupdate ( '{cms_digg}' )->set ( $data )->where ( array ('page_id' => $page_id ) )->exec ( true )) {
							// 没更新成功则新增
							$data ['page_id'] = $page_id;
							$data [$diggv] = 1;
							$data ['digg_total'] = 1;
							dbinsert ( $data )->into ( '{cms_digg}' )->exec ();
						}
						return true;
					} else {
						return 'page not found';
					}
				} else {
					return 'digged';
				}
			}
		} else {
			return '非法的digg值，合法的值为0-9.';
		}
	}
}